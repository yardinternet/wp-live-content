<?php

declare(strict_types=1);

it('returns 400 when id is not a valid post id', function (string $id) {
	$this->post('/yard/live-content/update?id=' . $id)
		->assertStatus(400);
})->with(['abc', '5.5', '5e2', '-1', '0']);

it('returns 403 when the nonce is missing', function () {
	\WP_Mock::userFunction('wp_verify_nonce', [
		'args' => ['', 'yard-live-content-update'],
		'return' => false,
	]);
	\WP_Mock::userFunction('current_user_can', [
		'return' => true,
	]);

	$this->post('/yard/live-content/update?id=5')
		->assertStatus(403);
});

it('returns 403 when the nonce is invalid', function () {
	\WP_Mock::userFunction('wp_verify_nonce', [
		'args' => ['invalid-nonce', 'yard-live-content-update'],
		'return' => false,
	]);
	\WP_Mock::userFunction('current_user_can', [
		'return' => true,
	]);

	$this->post('/yard/live-content/update?id=5', [], ['X-WP-Nonce' => 'invalid-nonce'])
		->assertStatus(403);
});

it('returns 403 when the user cannot edit the post', function () {
	\WP_Mock::userFunction('wp_verify_nonce', [
		'args' => ['valid-nonce', 'yard-live-content-update'],
		'return' => 1,
	]);
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => false,
	]);

	$this->post('/yard/live-content/update?id=5', [], ['X-WP-Nonce' => 'valid-nonce'])
		->assertStatus(403);
});

it('stores the push timestamp when the user can edit the post', function () {
	\WP_Mock::userFunction('wp_verify_nonce', [
		'args' => ['valid-nonce', 'yard-live-content-update'],
		'return' => 1,
	]);
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => true,
	]);
	\WP_Mock::userFunction('set_transient', [
		'times' => 1,
		'args' => ['post_updated_5', \Mockery::on(fn ($value) => is_int($value) && 1 < $value), DAY_IN_SECONDS],
		'return' => true,
	]);

	$this->post('/yard/live-content/update?id=5', [], ['X-WP-Nonce' => 'valid-nonce'])
		->assertStatus(200);
});
