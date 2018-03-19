<?php

$entity = elgg_extract('entity', $vars);

if (!$entity->location) {
	return;
}

$marker = \hypeJunction\MapsOpen\Marker::fromLocation($entity->location);
$marker->tooltip = $entity->location;

$output = elgg_view('page/components/map', [
	'center' => $marker,
	'markers' => [
		$marker,
	],
]);

echo elgg_view('post/module', [
	'title' => elgg_echo('maps:map'),
	'body' => $output,
	'collapsed' => false,
	'class' => 'post-map',
]);
