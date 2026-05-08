<?php

return [
	'plugin' => [
		'version' => '5.0.0',
	],

	'bootstrap' => \hypeJunction\MapsOpen\Bootstrap::class,

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
				__DIR__ . '/vendor/npm-asset/leaflet/dist/',
				__DIR__ . '/vendor/npm-asset/leaflet.awesome-markers/dist/',
				__DIR__ . '/vendor/npm-asset/leaflet.markercluster/dist/',
			],
		],
	],

	'upgrades' => [
		\hypeJunction\MapsOpen\Upgrades\GeocodeExistingEntityLocations::class,
		\hypeJunction\MapsOpen\Upgrades\MigrateLocationAnnotations::class,
	],

	'events' => [
		'fields' => [
			'object' => [\hypeJunction\MapsOpen\AddFormField::class => []],
			'group' => [\hypeJunction\MapsOpen\AddFormField::class => []],
			'user' => [\hypeJunction\MapsOpen\AddFormField::class => []],
		],
	],
];
