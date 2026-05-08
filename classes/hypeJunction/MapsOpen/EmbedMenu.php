<?php

namespace hypeJunction\MapsOpen;

use Elgg\Event;

class EmbedMenu {

	/**
	 * Setup embed menu
	 *
	 * @param Event $event Hook
	 * @return \ElggMenuItem[]
	 */
	public function __invoke(Event $event) {

		$menu = $event->getValue();

		$menu->add(\ElggMenuItem::factory([
			'name' => 'map',
			'text' => elgg_echo('embed:map'),
			'data' => [
				'view' => 'embed/tab/map',
			],
		]));
	}
}