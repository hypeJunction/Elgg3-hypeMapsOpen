<?php

namespace hypeJunction\MapsOpen;

class Users {

	/**
	 * Add map tab to members plugin nav
	 *
	 * @param string $hook   "members:config"
	 * @param string $type   "tabs"
	 * @param array  $return Tabs
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function addMapTab($hook, $type, $return, $params) {

		if (!elgg_get_plugin_setting('enable_user_map', 'hypeMapsOpen')) {
			return;
		}

		$return[] = \ElggMenuItem::factory([
			'name' => 'map',
			'text' => elgg_echo('maps:open:members:map'),
			'href' => elgg_generate_url('collection:user:user:map'),
		]);

		return $return;
	}

	/**
	 * Add location field to profile fields
	 *
	 * @param string $hook   "profile:fields"
	 * @param string $type   "profile"
	 * @param array  $return Fields
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function removeLocationField($hook, $type, $return, $params) {
		unset($return['location']);

		return $return;
	}

}
