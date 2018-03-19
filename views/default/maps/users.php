<?php

echo elgg_view('page/components/map', [
	'src' => elgg_generate_url('collection:user:user:map', [
		'view' => 'json',
	]),
	'show_search' => true,
	'zoom' => 5,
	'layer_options' => [
		'minZoom' => 5,
	],
]);
