<?php

namespace hypeJunction\MapsOpen;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function load(): void {
		$autoloader = dirname(__DIR__, 3) . '/autoloader.php';
		if (file_exists($autoloader)) {
			require_once $autoloader;
		}
	}

	public function init(): void {
		foreach (['user', 'object', 'group', 'site'] as $type) {
			elgg_register_event_handler('create', $type, [Geocoder::class, 'setEntityLatLong']);
			elgg_register_event_handler('update:after', $type, [Geocoder::class, 'setEntityLatLong']);
		}

		elgg_register_event_handler('geocode', 'location', [Geocoder::class, 'geocode']);
		elgg_register_event_handler('geocode', 'latlong', [Geocoder::class, 'reverse']);

		elgg_register_event_handler('tool_options', 'group', [Groups::class, 'filterToolOptions']);
		elgg_register_event_handler('profile:fields', 'group', [Groups::class, 'addLocationField']);
		elgg_register_event_handler('register', 'menu:filter:groups/all', [Groups::class, 'addMapTab']);

		elgg_register_event_handler('register', 'menu:filter:members', [Users::class, 'addMapTab']);
		elgg_register_event_handler('profile:fields', 'profile', [Users::class, 'removeLocationField']);

		elgg_register_event_handler('modules', 'object', [Post::class, 'addLocationModule']);
		elgg_register_event_handler('modules', 'group', [Post::class, 'addLocationModule']);
		elgg_register_event_handler('modules', 'user', [Post::class, 'addLocationModule']);

		elgg_register_event_handler('seeds', 'database', [Seeder::class, 'addSeed']);

		if (elgg()->has('group_tools')) {
			elgg()->group_tools->register('member_map', [
				'default_on' => false,
			]);
		}

		elgg_extend_view('forms/profile/edit', 'forms/profile/location');

		elgg_extend_view('elgg.css', 'leaflet.css');
		elgg_extend_view('elgg.css', 'leaflet.awesome-markers.css');
		elgg_extend_view('elgg.css', 'MarkerCluster.Default.css');
		elgg_extend_view('elgg.css', 'MarkerCluster.css');
		elgg_extend_view('elgg.css', 'page/components/map.css');

		elgg_register_esm('leaflet-markers', elgg_get_simplecache_url('leaflet.awesome-markers.min.js'));

		elgg_register_esm('leaflet-clusters', elgg_get_simplecache_url('leaflet.markercluster.js'));

		if (elgg()->has('shortcodes')) {
			elgg()->shortcodes->register('map');
			elgg_register_action('embed/map', EmbedAction::class);
			elgg_register_event_handler('register', 'menu:embed', EmbedMenu::class);
			elgg_register_event_handler('view_vars', 'river/elements/layout', EmbedRiverAttachment::class, 999);
		}
	}
}
