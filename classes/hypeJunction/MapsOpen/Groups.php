<?php

namespace hypeJunction\MapsOpen;

use Elgg\Event;

class Groups {

	/**
	 * Configure group tool options
	 *
	 * @param Event $event
	 * @return array
	 */
	public static function filterToolOptions(Event $event) {
		$return = $event->getValue();

		if (!elgg_get_plugin_setting('enable_group_member_map', 'hypemapsopen')) {
			foreach ($return as $key => $tool) {
				if ($tool->name == 'member_map') {
					unset($return[$key]);
				}
			}
		}

		return $return;
	}

	/**
	 * Add location field to group profile fields
	 *
	 * @param Event $event
	 * @return array
	 */
	public static function addLocationField(Event $event) {
		$return = $event->getValue();

		if (!elgg_get_plugin_setting('enable_group_map', 'hypemapsopen')) {
			return;
		}

		if (array_key_exists('location', $return)) {
			return;
		}

		$return['location'] = 'location';
		return $return;
	}

	/**
	 * Add group maps tab
	 *
	 * @param Event $event
	 */
	public static function addMapTab(Event $event) {
		if (!elgg_get_plugin_setting('enable_group_map', 'hypemapsopen')) {
			return;
		}

		$menu = $event->getValue();
		$menu->add(\ElggMenuItem::factory([
			'name' => 'groups:map',
			'text' => elgg_echo('maps:open:groups:tab'),
			'href' => elgg_generate_url('collection:group:group:map'),
			'priority' => 600,
		]));
	}
}
