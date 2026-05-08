<?php

namespace hypeJunction\MapsOpen;

use Elgg\Database\Seeds\Seed;
use Elgg\Event;

class Seeder extends Seed {

	public static function getType(): string {
		return 'hypemapsopen';
	}

	protected function getCountOptions(): array {
		return [];
	}

	/**
	 * Populate database
	 * @return mixed
	 */
	function seed() {

		$entities = elgg_get_entities([
			'limit' => 0,
			'batch' => true,
			'metadata_name_value_pairs' => [
				[
					'name' => '__faker',
					'value' => true,
				],
				[
					'name' => 'location',
					'comparison' => 'IS NULL',
					'value' => null,
				]
			],
		]);

		foreach ($entities as $entity) {
			$entity->location = $this->faker()->city;
			$entity->save();
		}
	}

	/**
	 * Removed seeded rows from database
	 * @return mixed
	 */
	function unseed() {

	}

	/**
	 * Register this seed
	 *
	 * @param Event $event
	 *
	 * @return array|mixed
	 */
	public static function addSeed(Event $event) {
		$value = $event->getValue();
		$value[] = __CLASS__;

		return $value;
	}
}
