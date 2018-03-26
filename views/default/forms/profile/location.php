<?php

$entity = elgg_extract('entity', $vars);
$location = elgg_extract('location', $vars);
if (!$location && $entity) {
	$location = $entity->location;
}

echo elgg_view_field([
	'#type' => 'location',
	'#label' => elgg_echo('profile:location'),
	'#help' => elgg_echo('profile:location:help'),
	'name' => 'location',
	'value' => $location ? : $entity ? $entity->location : '',
]);