<?php

declare(strict_types=1);

it('serves a javascript asset', function (string $path) {
	$this->get($path)
		->assertOk()
		->assertHeader('Content-Type', 'application/javascript');
})->with([
	'/yard/live-content/assets/js/htmx',
	'/yard/live-content/assets/js/editor',
]);
