<?php

if (!elgg()->has('shortcodes')) {
	return;
}

$svc = elgg()->shortcodes;
/* @var $svc \hypeJunction\Shortcodes\ShortcodesService */

$attrs = [
	'location' => elgg_extract('location', $vars),
	'zoom' => elgg_extract('zoom', $vars),
];

$tag = $svc->generate('map', $attrs);

$output = elgg_format_element('div', [
	'contenteditable' => 'false',
], $tag);

echo elgg_trigger_plugin_hook('prepare:map', 'embed', $vars, $output);
