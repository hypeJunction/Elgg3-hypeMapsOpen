<?php

namespace hypeJunction\MapsOpen;

use ElggEntity;
use hypeJunction\Fields\Field;
use Symfony\Component\HttpFoundation\ParameterBag;

class LocationField extends Field {

	/**
     * @param ElggEntity $entity
     * @param mixed $context
     * @return mixed
     */
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

	/**
     * @param ElggEntity $entity
     * @param ParameterBag $parameters
     * @return mixed
     */
    public function save(ElggEntity $entity, ParameterBag $parameters) {
		$svc = elgg()->{'posts.location'};

		/* @var $svc Post */

		return $svc->setGeoLocation($entity, $parameters->get($this->name));
	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
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
