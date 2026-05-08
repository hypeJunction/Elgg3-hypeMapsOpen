<?php

namespace hypeJunction\MapsOpen;

use Elgg\Event;

class Users {

	/**
	 * Add map tab to members plugin nav
	 *
	 * @param Event $event
	 */
	public static function addMapTab(Event $event) {
		if (!elgg_get_plugin_setting('enable_user_map', 'hypemapsopen')) {
			return;
		}

		$menu = $event->getValue();
		$menu->add(\ElggMenuItem::factory([
			'name' => 'map',
			'text' => elgg_echo('maps:open:members:map'),
			'href' => elgg_generate_url('collection:user:user:map'),
			'priority' => 800,
		]));
	}

	/**
	 * Remove location field from profile fields
	 *
	 * @param Event $event
	 * @return array
	 */
	public static function removeLocationField(Event $event) {
		$return = $event->getValue();
		unset($return['location']);
		return $return;
	}
}
