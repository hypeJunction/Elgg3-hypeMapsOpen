<?php

$location = elgg_extract('location', $vars);
$zoom = elgg_extract('zoom', $vars);

if (!$location) {
	return;
}

$marker = \hypeJunction\MapsOpen\Marker::fromLocation($location);

$output = elgg_view('page/components/map', [
	'center' => $marker,
	'markers' => [
		$marker,
	],
]);

echo elgg_format_element('div', [
	'class' => 'maps-embed',
], $output);