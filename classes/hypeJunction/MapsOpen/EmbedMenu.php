<?php

namespace hypeJunction\MapsOpen;

use Elgg\Hook;

class EmbedMenu {

	/**
	 * Setup embed menu
	 *
	 * @param Hook $hook Hook
	 * @return \ElggMenuItem[]
	 */
	public function __invoke(Hook $hook) {

		$menu = $hook->getValue();

		$menu[] = \ElggMenuItem::factory([
			'name' => 'map',
			'text' => elgg_echo('embed:map'),
			'data' => [
				'view' => 'embed/tab/map',
			],
		]);

		return $menu;
	}
}