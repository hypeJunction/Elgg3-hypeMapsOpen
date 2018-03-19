<?php

namespace hypeJunction\MapsOpen;

use Elgg\Hook;
use ElggEntity;

class Post {

	/**
	 * Set location
	 *
	 * @param ElggEntity $entity   Entity
	 * @param string     $location Location
	 *
	 * @return bool
	 */
	public function setGeoLocation(ElggEntity $entity, $location) {
		$entity->setVolatileData('location', $this->getGeoLocation($entity));

		$latlong = LatLong::fromLocation($location);

		$entity->location = $latlong->getLocation();
		$entity->{"geo:lat"} = $latlong->getLat();
		$entity->{"geo:long"} = $latlong->getLong();

		return elgg_trigger_event('update', 'object:location', $entity);
	}

	/**
	 * Retrieve location
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return LatLong|null
	 */
	public function getGeoLocation(ElggEntity $entity) {
		if ($entity->location) {
			return LatLong::fromLocation($entity->location);
		}

		if (isset($entity->{"geo:lat"}) && isset($entity->{"geo:long"})) {
			return LatLong::fromLatLong($entity->{"geo:lat"}, $entity->{"geo:long"});
		}

		return null;
	}

	/**
	 * Add location module to post profile
	 *
	 * @param Hook $hook
	 *
	 * @return mixed
	 */
	public static function addLocationModule(Hook $hook) {
		$value = $hook->getValue();

		$value['map'] = [
			'enabled' => true,
			'position' => 'sidebar',
			'priority' => 200,
		];

		return $value;
	}
}
