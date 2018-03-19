<?php

namespace hypeJunction\MapsOpen;

use Elgg\Hook;

class Groups {
	
	/**
	 * Configure group tool options
	 *
	 * @param string $hook   "tool_options"
	 * @param string $type   "group"
	 * @param array  $return Tool options
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function filterToolOptions($hook, $type, $return, $params) {

		if (!elgg_get_plugin_setting('enable_group_member_map', 'hypeMapsOpen')) {
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
	 * @param string $hook   "profile:fields"
	 * @param string $type   "group"
	 * @param array  $return Fields
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function addLocationField($hook, $type, $return, $params) {

		if (!elgg_get_plugin_setting('enable_group_map', 'hypeMapsOpen')) {
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
	 * @param Hook $hook
	 *
	 * @return array|mixed|void
	 */
	public static function addMapTab(Hook $hook) {
		if (!elgg_get_plugin_setting('enable_group_map', 'hypeMapsOpen')) {
			return;
		}

		$return = $hook->getValue();

		$return[] = \ElggMenuItem::factory([
			'name' => 'groups:map',
			'text' => elgg_echo('maps:open:groups:tab'),
			'href' => elgg_generate_url('collection:group:group:map'),
			'priority' => 600,
		]);

		return $return;
	}
}
