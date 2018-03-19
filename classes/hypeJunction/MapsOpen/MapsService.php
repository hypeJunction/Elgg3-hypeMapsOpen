<?php

namespace hypeJunction\MapsOpen;

use Elgg\Database\QueryBuilder;
use stdClass;

class MapsService {

	const COOKIE_NAME = 'geop';
	const KM_TO_MILE = 0.621371;
	const MILE_TO_KM = 1.60934;

	/**
	 * Geocodes a location
	 *
	 * Returns an array:
	 * <code>
	 * [
	 *    'lat' => $lat,
	 *    'long' => $long,
	 * ]
	 * </code>
	 *
	 * @param string $location Location
	 *
	 * @return array
	 */
	public function geocode($location = '') {
		return elgg_trigger_plugin_hook('geocode', 'location', ['location' => $location]);
	}

	/**
	 * Reverse geocode coordinates
	 *
	 * @param float $lat  Latitude
	 * @param float $long Longitude
	 * @param int   $zoom Zoom/precision level
	 *
	 * @return string
	 */
	public function reverse($lat, $long, $zoom = 12) {
		return elgg_trigger_plugin_hook('geocode', 'latlong', [
			'lat' => $lat,
			'long' => $long,
			'zoom' => $zoom,
		]);
	}

	/**
	 * Get coordinates and location name of the current session
	 * @return LatLong|null
	 */
	public function getSessionCoordinates() {
		if (isset($_COOKIE[self::COOKIE_NAME])) {
			$data = unserialize(base64_decode($_COOKIE[self::COOKIE_NAME]));

			return new LatLong($data['lat'], $data['long'], $data['location']);
		}

		return null;
	}

	/**
	 * Set session coordinates
	 *
	 * @param string $location  Location
	 * @param float  $latitude  Latitude
	 * @param float  $longitude Longitude
	 *
	 * @return stdClass
	 */
	public function setSessionCoordinates($location = '', $latitude = 0, $longitude = 0) {
		$lat = (float) $latitude;
		$long = (float) $longitude;

		if (!$lat || !$long) {
			$latlong = self::geocode($location);
			if ($latlong) {
				$lat = elgg_extract('lat', $latlong);
				$long = elgg_extract('long', $latlong);
			}
		}

		$geopositioning = [
			'location' => $location,
			'latitude' => $lat,
			'longitude' => $long
		];
		$cookie_value = base64_encode(serialize($geopositioning));

		$cookie = new \ElggCookie(self::COOKIE_NAME);
		$cookie->value = $cookie_value;
		elgg_set_cookie($cookie);

		return (object) $geopositioning;
	}

	/**
	 * Returns default map center
	 * @return LatLong
	 */
	public function getDefaultMapCenter() {
		$latlong = $this->getSessionCoordinates();
		if (!$latlong) {
			$user = elgg_get_logged_in_user_entity();
			if ($user && $user->location) {
				$latlong = LatLong::fromLocation($user->location);
			} else {
				$site_location = elgg_get_plugin_setting('site_location', 'hypeMapsOpen');
				$latlong = LatLong::fromLocation($site_location);
			}
		}

		return $latlong;
	}

	/**
	 * Export an entity into a map marker
	 *
	 * @param \ElggEntity $entity Entity
	 *
	 * @return Marker|false
	 */
	public function getMarker(\ElggEntity $entity) {

		$location = $entity->location;
		if (!$location) {
			return false;
		}

		$marker = Marker::fromLocation($location);

		$marker->title = $entity->getDisplayName();
		$marker->url = $entity->getURL();
		$marker->guid = $entity->guid;

		switch ($entity->getType()) {
			case 'user' :
				$marker->icon = 'user';
				$marker->color = 'red';
				break;
			case 'group' :
				$marker->icon = 'users';
				$marker->color = 'green';
				break;
			case 'object' :
				$marker->icon = 'sticky-note-o';
				$marker->color = 'blue';
				break;
		}

		$marker->tooltip = elgg_view_entity($entity, [
			'full_view' => false,
			'item_view' => 'maps/tooltip',
		]);

		$marker->distance = $entity->getVolatileData('select:proximity');

		return elgg_trigger_plugin_hook('marker', $entity->getType(), ['entity' => $entity], $marker);
	}

	/**
	 * Get markers for entities that match options
	 *
	 * @param array   $options  ege* options
	 * @param LatLong $location Location to search around
	 * @param int     $radius   Radius (in preferred unit)
	 * @param string  $query    Search query
	 *
	 * @return Marker[]
	 */
	public function getMarkers(array $options = [], LatLong $location = null, $radius = 0, $query = '') {
		if ($location) {
			$options = $this->addLocationSearchClauses($options, $location, $radius);
		}

		if ($query) {
			$options['query'] = $query;
			$options['search_type'] = 'entities';

			$entities = elgg_search($options);
		} else {
			$options['batch'] = true;
			$entities = elgg_get_entities($options);
		}

		$markers = [];
		foreach ($entities as $entity) {
			$markers[] = $this->getMarker($entity);
		}

		return array_filter($markers);
	}

	/**
	 * Add search clauses to options array
	 *
	 * @param array   $options  ege* options
	 * @param LatLong $location Location to search around
	 * @param int     $radius   Radius (in preferred unit)
	 *
	 * @return array
	 */
	public function addLocationSearchClauses(array $options = [], LatLong $location = null, $radius = 0) {

		if (!$location) {
			return $options;
		}

		$lat = (float) $location->getLat();
		$long = (float) $location->getLong();

		$options['order_by'] = false;

		$options['wheres'][] = function (QueryBuilder $qb) use ($lat, $long, $radius, $options) {
			$qb->joinMetadataTable('e', 'guid', 'geo:lat', 'inner', 'mdlat');
			$qb->joinMetadataTable('e', 'guid', 'geo:long', 'inner', 'mdlong');

			$lat = $qb->param($lat, ELGG_VALUE_STRING);
			$long = $qb->param($long, ELGG_VALUE_STRING);

			$proximity = "(((acos(sin(($lat*pi()/180))
					*sin((mdlat.value*pi()/180))+cos(($lat*pi()/180))
					*cos((mdlat.value*pi()/180))
					*cos((($long-mdlong.value)*pi()/180)))))*180/pi())*60*1.1515*1.60934";

			if (elgg_extract('order_by', $options) == 'proximity') {
				$qb->addSelect("$proximity AS proximity");
				$qb->addOrderBy('proximity', 'ASC');
			}

			$qb->addOrderBy('e.time_updated', 'DESC');

			if ($radius) {
				return $qb->compare($proximity, 'lte', $radius);
			}

		};

		$qb = \Elgg\Database\Select::fromTable('entities', 'e');
		$qb->select('guid')
			->where($qb->compare('e.time_created', '>=', strtotime('-1 year')));

		return $options;
	}

}
