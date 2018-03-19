<?php

echo elgg_view_field([
	'#type' => 'location',
	'#label' => elgg_echo('embed:map:location'),
	'name' => 'location',
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('embed:map:zoom'),
	'#help' => elgg_echo('embed:map:zoom:help'),
	'name' => 'zoom',
	'value' => 13,
	'options' => range(1, 18),
	'required' => true,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('embed:embed'),
]);

elgg_set_form_footer($footer);
