<?php

if (!elgg_get_plugin_setting('enable_user_map', 'hypeMapsOpen')) {
	throw new \Elgg\HttpException();
}

$title = elgg_echo('maps:open:users');

$content = elgg_view('maps/users');

$layout = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'sidebar' => false,
	'filter_id' => 'members',
	'filter_value' => 'map',
		]);

echo elgg_view_page($title, $layout);
