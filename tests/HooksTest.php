<?php

declare(strict_types=1);

use Yard\LiveContent\Hooks;

it('does not add the admin bar button when the user cannot edit the post', function () {
	$post = Mockery::mock('WP_Post');
	$post->ID = 5;
	$post->post_type = 'openpub-item';

	$wpAdminBar = Mockery::mock('WP_Admin_Bar');
	$wpAdminBar->shouldNotReceive('add_menu');

	$GLOBALS['post'] = $post;
	$GLOBALS['wp_admin_bar'] = $wpAdminBar;

	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => false,
	]);

	(new Hooks())->addButtonToAdminBar();

	unset($GLOBALS['post'], $GLOBALS['wp_admin_bar']);
});

it('sends a nonce header with the push request from the admin bar button', function () {
	$post = Mockery::mock('WP_Post');
	$post->ID = 5;
	$post->post_type = 'openpub-item';

	$wpAdminBar = Mockery::mock('WP_Admin_Bar');
	$wpAdminBar->shouldReceive('add_menu')
		->once()
		->with(Mockery::on(fn (array $menu) => str_contains($menu['meta']['onclick'], '"X-WP-Nonce": "test-nonce"')));

	$GLOBALS['post'] = $post;
	$GLOBALS['wp_admin_bar'] = $wpAdminBar;

	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => true,
	]);
	\WP_Mock::userFunction('wp_create_nonce', [
		'args' => ['yard-live-content-update'],
		'return' => 'test-nonce',
	]);

	(new Hooks())->addButtonToAdminBar();

	unset($GLOBALS['post'], $GLOBALS['wp_admin_bar']);
});
