<?php

return [
	'maps' => \DI\create(\hypeJunction\MapsOpen\MapsService::class),
	'posts.location' => \DI\create(\hypeJunction\MapsOpen\Post::class),
];
