<?php

namespace hypeJunction\MapsOpen;

use Elgg\Database\Seeds\Seed;
use Elgg\Hook;

class Seeder extends Seed {

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
	 * @param Hook $hook
	 *
	 * @return array|mixed
	 */
	public static function addSeed(Hook $hook) {
		$value = $hook->getValue();
		$value[] = __CLASS__;

		return $value;
	}
}
