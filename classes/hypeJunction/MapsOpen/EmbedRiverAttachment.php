<?php

namespace hypeJunction\MapsOpen;

use Elgg\Hook;

class EmbedRiverAttachment {

	/**
	 * Add player preview to river items
	 *
	 * @param $hook Hook Hook
	 * @return array|null
	 */
	public function __invoke(Hook $hook) {

		$vars = $hook->getValue();

		if (!empty($vars['attachments'])) {
			return null;
		}

		$item = elgg_extract('item', $vars);
		if (!$item instanceof \ElggRiverItem) {
			return null;
		}

		$object = $item->getObjectEntity();
		if (!$object instanceof \ElggObject) {
			return null;
		}

		$description = $object->description;
		if (!$description) {
			return null;
		}

		$svc = elgg()->shortcodes;
		/* @var $svc \hypeJunction\Shortcodes\ShortcodesService */

		$matches = $svc->extract($description);

		if (!empty($matches['map'][0])) {
			$vars['attachments'] = elgg_format_element('div', [
				'class' => 'embed-map-listing-preview elgg-river-attachment',
			], elgg_view('shortcodes/map', $matches['map'][0]));

			return $vars;
		}

		if ($object->location) {
			$vars['attachments'] = elgg_format_element('div', [
				'class' => 'embed-map-listing-preview elgg-river-attachment',
			], elgg_view('shortcodes/map', [
				'location' => $object->location,
				'zoom' => 13,
			]));
		}

		return $vars;
	}
}