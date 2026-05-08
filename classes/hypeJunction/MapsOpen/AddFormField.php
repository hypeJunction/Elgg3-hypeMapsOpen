<?php

namespace hypeJunction\MapsOpen;

use DateTime;
use DateTimeZone;
use Elgg\Event;
use Elgg\Request;
use ElggEntity;
use hypeJunction\Fields\Collection;

class AddFormField {

	/**
	 * Add slug field
	 *
	 * @param Event $event Hook
	 *
	 * @return mixed
	 * @throws \InvalidParameterException
	 */
	public function __invoke(Event $event) {

		$fields = $event->getValue();
		/* @var $fields Collection */

		$fields->add('location', new LocationField([
			'type' => 'location',
			'priority' => 450,
		]));

		return $fields;
	}
}
