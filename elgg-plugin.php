<?php

$plugin_root = __DIR__;
$root = dirname(dirname($plugin_root));
$alt_root = dirname(dirname(dirname($root)));

if (file_exists("$plugin_root/vendor/autoload.php")) {
	$path = $plugin_root;
} else if (file_exists("$root/vendor/autoload.php")) {
	$path = $root;
} else {
	$path = $alt_root;
}

return [
	'routes' => [
		'collection:user:user:map' => [
			'path' => '/members/map',
			'resource' => 'maps/users',
		],
		'collection:group:group:map' => [
			'path' => '/groups/map',
			'resource' => 'maps/groups',
		],
		'view:group:group:members:map' => [
			'path' => '/groups/profile/{guid}/members/map',
			'resource' => 'maps/members',
		],
	],
	'views' => [
		'default' => [
			'/' => [
				$path . '/vendor/npm-asset/leaflet/dist/',
				$path . '/vendor/npm-asset/leaflet.awesome-markers/dist/',
				$path . '/vendor/npm-asset/leaflet.markercluster/dist/',
			],
		],
	],
];
