<?php

$elggRoot = '/var/www/html';

require_once $elggRoot . '/vendor/autoload.php';

$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
	$file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

$app = \Elgg\Application::getInstance();
$app->bootCore();

\Elgg\IntegrationTestCase::$_testing_app = $app;

if (function_exists('_elgg_services')) {
	_elgg_services()->plugins->generateEntities();
	$plugin = elgg_get_plugin_from_id('hypemapsopen');
	if ($plugin && !$plugin->isActive()) {
		try {
			$plugin->setPriority('last');
			$plugin->activate();
		} catch (\Throwable $e) {
			echo 'WARNING: could not activate hypemapsopen: ' . $e->getMessage() . PHP_EOL;
		}
	}
}
