<?php

namespace hypeJunction\MapsOpen;

use ElggEntity;
use hypeJunction\Fields\Field;
use Symfony\Component\HttpFoundation\ParameterBag;

class LocationField extends Field {

	public function isVisible(ElggEntity $entity, $context = null) {

		$params = [
			'entity' => $entity,
		];

		$enabled = elgg()->hooks->trigger(
			'uses:location',
			"$entity->type:$entity->subtype",
			$params,
			false
		);
		if (!$enabled) {
			return false;
		}

		return parent::isVisible($entity, $context);
	}

	public function save(ElggEntity $entity, ParameterBag $parameters) {
		$svc = elgg()->{'posts.location'};

		/* @var $svc Post */

		return $svc->setGeoLocation($entity, $parameters->get($this->name));
	}

	public function retrieve(ElggEntity $entity) {
		$svc = elgg()->{'posts.location'};
		/* @var $svc Post */

		$location = $svc->getGeoLocation($entity);
		if ($location) {
			return $location->getLocation();
		}

		return null;
	}
}
