<?php

$group = elgg_get_page_owner_entity();

try {
	elgg_entity_gatekeeper($group->guid, 'group');
} catch (Exception $ex) {
	return;
}

if ($group->member_map_enable == 'no') {
	return;
}

$all_link = elgg_view('output/url', [
	'href' => "groups/members/$group->guid",
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
]);

$content = elgg_view('page/components/map', [
	'src' => elgg_generate_url('view:group:group:members:map', [
		'guid' => $group->guid,
		'view' => 'json',
	]),
	'show_search' => false,
	'zoom' => 3,
	'layer_options' => [
		'minZoom' => 3,
	],
]);

echo elgg_view('groups/profile/module', [
	'title' => elgg_echo('groups:members'),
	'content' => $content,
	'all_link' => $all_link,
]);
