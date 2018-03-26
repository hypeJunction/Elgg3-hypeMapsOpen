<?php

namespace hypeJunction\MapsOpen;

use DateTime;
use DateTimeZone;
use Elgg\Hook;
use Elgg\Request;
use ElggEntity;

class AddFormField {

	/**
	 * Add slug field
	 *
	 * @param Hook $hook Hook
	 *
	 * @return mixed
	 */
	public function __invoke(Hook $hook) {

		$fields = $hook->getValue();

		$fields['location'] = [
			'#type' => 'location',
			'#setter' => function (ElggEntity $entity, $value) {
				$svc = elgg()->{'posts.location'};

				/* @var $svc Post */

				return $svc->setGeoLocation($entity, $value);
			},
			'#getter' => function (ElggEntity $entity) {
				$svc = elgg()->{'posts.location'};
				/* @var $svc Post */

				$location = $svc->getGeoLocation($entity);
				if ($location) {
					return $location->getLocation();
				}

				return null;
			},
			'#priority' => 450,
			'#section' => 'content',
			'#visibility' => function (ElggEntity $entity) use ($hook) {
				$params = [
					'entity' => $entity,
				];

				return $hook->elgg()->hooks->trigger(
					'uses:location',
					"$entity->type:$entity->subtype",
					$params,
					false
				);
			},
		];

		return $fields;
	}
}
