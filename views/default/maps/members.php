<?php

$group = elgg_extract('group', $vars);
try {
	elgg_entity_gatekeeper($group->guid, 'group');
} catch (Exception $ex) {
	return;
}

echo elgg_view('page/components/map', [
	'src' => elgg_generate_url('view:group:group:members:map', [
		'guid' => $group->guid,
		'view' => 'json',
	]),
	'show_search' => true,
	'zoom' => 5,
	'layer_options' => [
		'minZoom' => 5,
	],
]);
