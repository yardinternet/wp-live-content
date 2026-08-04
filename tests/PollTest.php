<?php

declare(strict_types=1);

it('returns 400 when id is missing', function () {
	$this->get('/yard/live-content/poll?since=100')
		->assertStatus(400);
});

it('returns 400 when since is missing', function () {
	$this->get('/yard/live-content/poll?id=5')
		->assertStatus(400);
});

it('returns 400 when id is not numeric', function () {
	$this->get('/yard/live-content/poll?id=abc&since=100')
		->assertStatus(400);
});

it('returns 400 when since is not numeric', function () {
	$this->get('/yard/live-content/poll?id=5&since=abc')
		->assertStatus(400);
});

it('returns 404 when the post does not exist', function () {
	\WP_Mock::userFunction('get_post', [
		'args' => [5],
		'return' => null,
	]);

	$this->get('/yard/live-content/poll?id=5&since=100')
		->assertStatus(404);
});

it('returns 204 when no push has been sent', function () {
	\WP_Mock::userFunction('get_post', [
		'args' => [5],
		'return' => Mockery::mock('WP_Post'),
	]);
	\WP_Mock::userFunction('get_transient', [
		'args' => ['post_updated_5'],
		'return' => false,
	]);

	$response = $this->get('/yard/live-content/poll?id=5&since=100');

	$response->assertStatus(204);
	expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

it('returns 204 when the last push is older than since', function () {
	\WP_Mock::userFunction('get_post', [
		'args' => [5],
		'return' => Mockery::mock('WP_Post'),
	]);
	\WP_Mock::userFunction('get_transient', [
		'args' => ['post_updated_5'],
		'return' => 50,
	]);

	$this->get('/yard/live-content/poll?id=5&since=100')
		->assertStatus(204);
});

it('returns the notification button when a push is newer than since', function () {
	\WP_Mock::userFunction('get_post', [
		'args' => [5],
		'return' => Mockery::mock('WP_Post'),
	]);
	\WP_Mock::userFunction('get_transient', [
		'args' => ['post_updated_5'],
		'return' => 150,
	]);

	$response = $this->get('/yard/live-content/poll?id=5&since=100');

	$response->assertStatus(200);
	$response->assertSee('hx-post="/yard/live-content/content?id=5"', false);
	$response->assertSee('Er is 1 nieuwe update', false);
	expect($response->headers->get('Cache-Control'))->toContain('no-store');
});
