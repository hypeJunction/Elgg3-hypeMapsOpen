<?php

return [

	'maps:map' => 'Map',

	'maps:open:setting:site_location' => 'Site Location',
	'maps:open:setting:site_location:help' => 'Used to determine default map centering, if user location is unknown',

	'maps:open:setting:enable_user_map' => 'Enable map of users',
	'maps:open:setting:enable_group_map' => 'Enable map of groups',
	'maps:open:setting:enable_group_member_map' => 'Enable group member map',

	'maps:open:users' => 'Users map',
	'maps:open:groups' => 'Groups map',
	'maps:open:members:map' => 'Map',
	'maps:open:groups:tab' => 'Map',
	'maps:open:members' => 'Members of %s',

	'maps:open:distance:km' => '%s km',

	'groups:tool:member_map' => 'Enable member map',

	'maps:open:search:location' => 'Location',
	'maps:open:search:radius' => 'Radius',
	'maps:open:search:query' => 'Keyword',

	'groups:location' => 'Location',

	'field:location' => 'Location',
	'field:location:help' => 'Tag this post with a geographical location',

	'embed:map' => 'Map',
	'embed:map:location' => 'Location',
	'embed:map:zoom' => 'Zoom',
	'embed:map:zoom:help' => 'Zoom levels range from 1 (entire surface of the planet) to 5-6 (country level) to 11-13 (city level) to 18 (detailed street level)',

	'profile:location:help' => 'Your location information is public, limit it to your city, region and country',

	'hypeMapsOpen:upgrade:2018032601:title' => 'Revert profile location storage to metadata',
	'hypeMapsOpen:upgrade:2018032601:description' => 'Latest Elgg version has migrated profile data storage to annotations, which is incompatible with lat/long storage. This will revert location storage changes',

	'hypeMapsOpen:upgrade:2018012101:title' => 'Geocode locations',
	'hypeMapsOpen:upgrade:2018012101:description' => 'Scan all entities with a set location that are missing geo coordinates and geocode them to make sure they are displayed on maps',

];