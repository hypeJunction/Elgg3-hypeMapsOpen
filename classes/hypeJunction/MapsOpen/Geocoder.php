<?php

namespace hypeJunction\MapsOpen;

use Elgg\Database\QueryBuilder;
use ElggEntity;
use ElggFile;

class Geocoder {

	/**
	 * Geocode location via Nominatim
	 *
	 * @param string $hook   "geocode"
	 * @param string $type   "location"
	 * @param mixed  $return Lat/long
	 * @param array  $params Hook params
	 *
	 * @return mixed
	 */
	public static function geocode($hook, $type, $return, $params) {

		if (!empty($return)) {
			// location has been geocoded elsewhere
			return;
		}

		$location = elgg_extract('location', $params);

		// Try geocache
		$site = elgg_get_site_entity();
		$location_hash = md5($location);

		$file = new ElggFile();
		$file->owner_guid = $site->guid;
		$file->setFilename("nominatim/$location_hash.json");

		if ($file->exists()) {
			$file->open('read');
			$json = $file->grabFile();
			$file->close();
		} else {
			$endpoint = elgg_http_add_url_query_elements('http://nominatim.openstreetmap.org/search', [
				'q' => $location,
				'format' => 'json',
				'email' => elgg_get_site_entity()->email,
				'limit' => 1,
				'namedetails' => false,
			]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$json = curl_exec($ch);
			curl_close($ch);

			$file->open('write');
			$file->write($json);
			$file->close();
		}

		if (!$json) {
			return;
		}

		$data = json_decode($json, true);

		if (empty($data)) {
			return;
		}

		$item = array_shift($data);

		return [
			'lat' => $item['lat'],
			'long' => $item['lon'],
		];
	}

	/**
	 * Geocode location via Nominatim
	 *
	 * @param string $hook   "geocode"
	 * @param string $type   "location"
	 * @param string $return Location
	 * @param array  $params Hook params
	 *
	 * @return mixed
	 */
	public static function reverse($hook, $type, $return, $params) {

		if (!empty($return)) {
			return;
		}

		$lat = elgg_extract('lat', $params);
		$long = elgg_extract('long', $params);
		$zoom = elgg_extract('zoom', $params, 12);

		// Try geocache
		$site = elgg_get_site_entity();
		$hash = md5("$lat:$long:$zoom");

		$file = new ElggFile();
		$file->owner_guid = $site->guid;
		$file->setFilename("nominatim/$hash.json");

		if ($file->exists()) {
			$file->open('read');
			$json = $file->grabFile();
			$file->close();
		} else {
			$endpoint = elgg_http_add_url_query_elements('http://nominatim.openstreetmap.org/reverse', [
				'lat' => $lat,
				'lon' => $long,
				'format' => 'json',
				'email' => elgg_get_site_entity()->email,
			]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$json = curl_exec($ch);
			curl_close($ch);

			$file->open('write');
			$file->write($json);
			$file->close();
		}

		if (!$json) {
			return;
		}

		$data = json_decode($json, true);

		if (empty($data)) {
			return;
		}

		return elgg_extract('display_name', $data);
	}

	/**
	 * Check if entity location has changed and geocode if so
	 *
	 * @param string     $event  "create"|"update"
	 * @param string     $type   "object"|"user"|"group"
	 * @param ElggEntity $entity Entity
	 */
	public static function setEntityLatLong($event, $type, $entity) {

		$location = get_input('location');
		if ($location && $entity->location != $location) {
			$entity->setLocation($location);
		}

		if ($entity->geocoded_location == $entity->location) {
			return;
		}

		if (is_array($entity->location) || !$entity->location) {
			return;
		}

		// Clear previous values
		unset($entity->{"geo:lat"});
		unset($entity->{"geo:long"});

		$entity->geocoded_location = $entity->location;

		$svc = elgg()->maps;
		/* @var $svc MapsService */

		$coordinates = $svc->geocode($entity->location);
		$lat = elgg_extract('lat', $coordinates) ? : '';
		$long = elgg_extract('long', $coordinates) ? : '';

		$entity->setLatLong($lat, $long);
	}

	/**
	 * Update entity geocoordinates
	 *
	 * @param int $offset Offset
	 *
	 * @return int
	 */
	public static function setBatchLatLong($offset = 0) {

		set_time_limit(0);

		$entities = self::getEntitiesWithoutGeocodes([
			'batch' => true,
			'offset' => $offset,
		]);

		$entities->setIncrementOffset(false);

		$i = 0;
		foreach ($entities as $e) {
			// trigger update
			$e->save();
			$lat = $e->getLatitude();
			$long = $e->getLongitude();
			if ($lat && $long) {
				elgg_log("New coordinates for {$e->getDisplayName()} ({$e->type}:{$e->getSubtype()} $e->guid) [$lat, $long]");
			}
			$i++;
		}

		return $i;
	}

	/**
	 * Get entities that are missing geographic coordinates
	 *
	 * @param array $options ege* options
	 *
	 * @return ElggEntity[]|false
	 */
	public static function getEntitiesWithoutGeocodes(array $options = []) {

		$exclude = [
			'messages',
			'plugin',
			'widget',
			'site_notification',
			'admin_notice',
		];

		$options['wheres'] = function (QueryBuilder $qb) use ($exclude) {
			$qb->joinMetadataTable('e', 'guid', 'location', 'inner', 'location');
			$qb->joinMetadataTable('e', 'guid', 'geo:lat', 'left', 'latitude');
			$qb->joinMetadataTable('e', 'guid', 'geo:long', 'left', 'longitude');

			$qb->merge([
				$qb->compare('e.subtype', 'not in', $exclude, ELGG_VALUE_STRING),
				$qb->compare('location.value', '!=', '', ELGG_VALUE_STRING),
				$qb->merge([
					$qb->compare('latitude.value', 'is null'),
					$qb->compare('longitude.value', 'is null'),
				], 'OR')
			]);
		};

		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () use ($options) {
			return elgg_get_entities($options);
		});
	}

}
