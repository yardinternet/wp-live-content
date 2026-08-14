<?php

declare(strict_types=1);

use Yard\LiveContent\Hooks;

function screen(string $postType): stdClass
{
	$screen = new stdClass();
	$screen->post_type = $postType;

	return $screen;
}

function editorPost(int $id): Mockery\MockInterface
{
	$post = Mockery::mock('WP_Post');
	$post->ID = $id;

	return $post;
}

it('does not enqueue the editor script for post types outside the config', function () {
	config()->set('wp-live-content.post-types', ['openpub-item']);

	\WP_Mock::userFunction('get_current_screen', ['return' => screen('page')]);
	\WP_Mock::userFunction('get_post', ['return' => editorPost(5)]);
	\WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);

	(new Hooks())->enqueueBlockEditorAssets();
});

it('does not enqueue the editor script when the user cannot edit the post', function () {
	config()->set('wp-live-content.post-types', ['openpub-item']);

	\WP_Mock::userFunction('get_current_screen', ['return' => screen('openpub-item')]);
	\WP_Mock::userFunction('get_post', ['return' => editorPost(5)]);
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => false,
	]);
	\WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);

	(new Hooks())->enqueueBlockEditorAssets();
});

it('enqueues the editor script and inlines the nonce before it', function () {
	config()->set('wp-live-content.post-types', ['openpub-item']);
	config()->set('app.url', 'https://example.com');

	\WP_Mock::userFunction('get_current_screen', ['return' => screen('openpub-item')]);
	\WP_Mock::userFunction('get_post', ['return' => editorPost(5)]);
	\WP_Mock::userFunction('current_user_can', [
		'args' => ['edit_post', 5],
		'return' => true,
	]);
	\WP_Mock::userFunction('wp_create_nonce', [
		'args' => ['yard-live-content-update'],
		'return' => 'test-nonce',
	]);
	\WP_Mock::userFunction('wp_json_encode', ['return' => fn (array $data) => json_encode($data)]);

	\WP_Mock::userFunction('wp_enqueue_script', [
		'times' => 1,
		'args' => [
			'yard-live-content-editor',
			'https://example.com/yard/live-content/assets/js/editor',
			['wp-components', 'wp-data', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-plugins'],
			Mockery::type('string'),
			true,
		],
	]);
	\WP_Mock::userFunction('wp_add_inline_script', [
		'times' => 1,
		'args' => [
			'yard-live-content-editor',
			'window.yardLiveContent = {"nonce":"test-nonce"};',
			'before',
		],
	]);

	(new Hooks())->enqueueBlockEditorAssets();
});
