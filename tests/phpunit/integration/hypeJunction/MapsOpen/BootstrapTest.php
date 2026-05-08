<?php

namespace hypeJunction\MapsOpen;

use Elgg\IntegrationTestCase;

class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypemapsopen';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPluginIsActive(): void {
		$plugin = elgg_get_plugin_from_id('hypemapsopen');
		$this->assertNotNull($plugin);
		$this->assertTrue($plugin->isActive());
	}

	public function testRoutesAreRegistered(): void {
		$routes = _elgg_services()->routes;
		$this->assertNotNull($routes->get('collection:user:user:map'));
		$this->assertNotNull($routes->get('collection:group:group:map'));
		$this->assertNotNull($routes->get('view:group:group:members:map'));
	}

	public function testGeocoderEventsRegistered(): void {
		$events = _elgg_services()->events;
		$this->assertTrue($events->hasHandler('geocode', 'location'));
		$this->assertTrue($events->hasHandler('geocode', 'latlong'));
		$this->assertTrue($events->hasHandler('create', 'user'));
		$this->assertTrue($events->hasHandler('create', 'object'));
		$this->assertTrue($events->hasHandler('update:after', 'user'));
	}

	public function testFieldsEventsRegistered(): void {
		$events = _elgg_services()->events;
		$this->assertTrue($events->hasHandler('fields', 'object'));
		$this->assertTrue($events->hasHandler('fields', 'group'));
		$this->assertTrue($events->hasHandler('fields', 'user'));
	}

	public function testModulesEventsRegistered(): void {
		$events = _elgg_services()->events;
		$this->assertTrue($events->hasHandler('modules', 'object'));
		$this->assertTrue($events->hasHandler('modules', 'group'));
	}
}
