<?php

namespace hypeJunction\MapsOpen;

use DateTime;
use DateTimeZone;
use Elgg\Hook;
use Elgg\Request;
use ElggEntity;
use hypeJunction\Fields\Collection;

class AddFormField {

	/**
	 * Add slug field
	 *
	 * @param Hook $hook Hook
	 *
	 * @return mixed
	 * @throws \InvalidParameterException
	 */
	public function __invoke(Hook $hook) {

		$fields = $hook->getValue();
		/* @var $fields Collection */

		$fields->add('location', new LocationField([
			'type' => 'location',
			'priority' => 450,
		]));

		return $fields;
	}
}
