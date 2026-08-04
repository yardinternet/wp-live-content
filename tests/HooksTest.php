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
