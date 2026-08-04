<?php

declare(strict_types=1);

it('returns 400 when id is not numeric', function () {
	$this->post('/yard/live-content/update?id=abc')
		->assertStatus(400);
});

it('returns 403 when the user cannot edit the post', function () {
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => false,
	]);

	$this->post('/yard/live-content/update?id=5')
		->assertStatus(403);
});

it('stores the push timestamp when the user can edit the post', function () {
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => true,
	]);
	\WP_Mock::userFunction('set_transient', [
		'times' => 1,
		'args' => ['post_updated_5', \Mockery::on(fn ($value) => is_int($value) && 1 < $value)],
		'return' => true,
	]);

	$this->post('/yard/live-content/update?id=5')
		->assertStatus(200);
});
