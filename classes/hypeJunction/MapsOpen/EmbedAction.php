<?php

namespace hypeJunction\MapsOpen;

use Elgg\BadRequestException;
use Elgg\Request;

class EmbedAction {

	/**
	 * Get safe embed code
	 *
	 * @param Request $request Request
	 *
	 * @return \Elgg\Http\OkResponse
	 * @throws BadRequestException
	 */
	public function __invoke(Request $request) {

		$output = elgg_view('embed/safe/map', [
			'location' => $request->getParam('location'),
			'zoom' => $request->getParam('zoom'),
		]);

		if (empty($output)) {
			throw new BadRequestException();
		}

		return elgg_ok_response($output);
	}
}