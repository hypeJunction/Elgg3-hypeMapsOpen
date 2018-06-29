<?php

/**
 * hypeMapsOpen
 *
 * Maps built with open tech
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2017-2018, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

use hypeJunction\MapsOpen\AddFormField;
use hypeJunction\MapsOpen\Geocoder;
use hypeJunction\MapsOpen\Groups;
use hypeJunction\MapsOpen\Post;
use hypeJunction\MapsOpen\Seeder;
use hypeJunction\MapsOpen\Users;

return function() {
	elgg_register_event_handler('init', 'system', function () {

		// Implement geocoding via Nominatim
		elgg_register_plugin_hook_handler('geocode', 'location', [Geocoder::class, 'geocode']);
		elgg_register_plugin_hook_handler('geocode', 'latlong', [Geocoder::class, 'reverse']);

		// Geocode entity location whenever it's created or updated
		foreach (['user', 'object', 'group', 'site'] as $type) {
			elgg_register_event_handler('create', $type, [Geocoder::class, 'setEntityLatLong']);
			elgg_register_event_handler('update:after', $type, [Geocoder::class, 'setEntityLatLong']);
		}

		// Groups
		elgg()->group_tools->register('member_map', [
			'default_on' => false,
		]);

		elgg_register_plugin_hook_handler('tool_options', 'group', [Groups::class, 'filterToolOptions']);
		elgg_register_plugin_hook_handler('profile:fields', 'group', [Groups::class, 'addLocationField']);
		elgg_register_plugin_hook_handler('register', 'menu:filter:groups/all', [Groups::class, 'addMapTab']);

		// Users
		elgg_register_plugin_hook_handler('register', 'menu:filter:members', [Users::class, 'addMapTab']);
		elgg_register_plugin_hook_handler('profile:fields', 'profile', [Users::class, 'removeLocationField']);
		elgg_extend_view('forms/profile/edit', 'forms/profile/location');

		// CSS
		elgg_extend_view('elgg.css', 'leaflet.css');
		elgg_extend_view('elgg.css', 'leaflet.awesome-markers.css');
		elgg_extend_view('elgg.css', 'MarkerCluster.Default.css');
		elgg_extend_view('elgg.css', 'MarkerCluster.css');

		elgg_extend_view('elgg.css', 'page/components/map.css');

		elgg_define_js('leaflet-markers', [
			'src' => elgg_get_simplecache_url('leaflet.awesome-markers.min.js'),
			'deps' => ['leaflet'],
		]);

		elgg_define_js('leaflet-clusters', [
			'src' => elgg_get_simplecache_url('leaflet.markercluster.js'),
			'deps' => ['leaflet'],
		]);

		elgg_register_plugin_hook_handler('seeds', 'database', [Seeder::class, 'addSeed']);

		elgg_register_plugin_hook_handler('fields', 'object', AddFormField::class);
		elgg_register_plugin_hook_handler('fields', 'group', AddFormField::class);
		elgg_register_plugin_hook_handler('fields', 'user', AddFormField::class);

		elgg_register_plugin_hook_handler('modules', 'object', [Post::class, 'addLocationModule']);
		elgg_register_plugin_hook_handler('modules', 'group', [Post::class, 'addLocationModule']);
		elgg_register_plugin_hook_handler('modules', 'user', [Post::class, 'addLocationModule']);

		if (elgg()->has('shortcodes')) {
			elgg()->shortcodes->register('map');
			elgg_register_action('embed/map', \hypeJunction\MapsOpen\EmbedAction::class);
			elgg_register_plugin_hook_handler('register', 'menu:embed', \hypeJunction\MapsOpen\EmbedMenu::class);
			elgg_register_plugin_hook_handler('view_vars', 'river/elements/layout', \hypeJunction\MapsOpen\EmbedRiverAttachment::class, 999);
		}
	});
};