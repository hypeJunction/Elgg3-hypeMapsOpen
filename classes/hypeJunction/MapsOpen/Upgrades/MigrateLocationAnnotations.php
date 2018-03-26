<?php

namespace hypeJunction\MapsOpen\Upgrades;

use Elgg\Upgrade\Batch;
use Elgg\Upgrade\Result;
use hypeJunction\MapsOpen\Geocoder;

class MigrateLocationAnnotations implements Batch {


	/**
	 * Version of the upgrade
	 *
	 * This tells the date when the upgrade was added. It consists of eight digits and is in format ``yyyymmddnn``
	 * where:
	 *
	 * - ``yyyy`` is the year
	 * - ``mm`` is the month (with leading zero)
	 * - ``dd`` is the day (with leading zero)
	 * - ``nn`` is an incrementing number (starting from ``00``) that is used in case two separate upgrades
	 *          have been added during the same day
	 *
	 * @return int E.g. 2016123101
	 */
	public function getVersion() {
		return 2018032601;
	}

	/**
	 * Should this upgrade be skipped?
	 *
	 * If true, the upgrade will not be performed and cannot be accessed later.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function shouldBeSkipped() {
		$count = $this->countItems();
		return empty($count);
	}

	/**
	 * Should the run() method receive an offset representing all processed items?
	 *
	 * If true, run() will receive as $offset the number of items already processed. This is useful
	 * if you are only modifying data, and need to use the $offset in a function like elgg_get_entities*()
	 * to know how many to skip over.
	 *
	 * If false, run() will receive as $offset the total number of failures. This should be used if your
	 * process deletes or moves data out of the way of the process. E.g. if you delete 50 objects on each
	 * run(), you may still use the $offset to skip objects that already failed once.
	 *
	 * @return bool
	 */
	public function needsIncrementOffset() {
		return true;
	}

	/**
	 * The total number of items to process during the upgrade
	 *
	 * If unknown, Batch::UNKNOWN_COUNT should be returned, and run() must manually mark the result
	 * as complete.
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function countItems() {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function() {
			return elgg_get_entities([
				'types' => 'user',
				'annotation_names' => 'profile:location',
				'count' => true,
			]);
		});
	}

	/**
	 * Runs upgrade on a single batch of items
	 *
	 * If countItems() returns Batch::UNKNOWN_COUNT, this method must call $result->markCompleted()
	 * when the upgrade is complete.
	 *
	 * @param Result $result Result of the batch (this must be returned)
	 * @param int    $offset Number to skip when processing
	 *
	 * @return Result Instance of \Elgg\Upgrade\Result
	 * @throws \Exception
	 */
	public function run(Result $result, $offset) {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function() use ($result, $offset) {
			$users = elgg_get_entities([
				'types' => 'user',
				'annotation_names' => 'profile:location',
				'batch' => true,
				'offset' => $offset,
			]);

			foreach ($users as $user) {
				$annotations = $user->getAnnotations([
					'annotation_names' => 'profile:location',
					'limit' => 1,
				]);

				if ($annotations) {
					$location = $annotations[0]->value;
					$user->location = $location;
					if ($user->save()) {
						$result->addSuccesses();
						$annotations[0]->delete;
					} else {
						$result->addFailures();
					}

				}
			}

			return $result;
		});
	}
}