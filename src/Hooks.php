<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Yard\Hook\Action;
use Yard\LiveContent\Traits\Helpers;

class Hooks
{
	use Helpers;

	private const EDITOR_HANDLE = 'yard-live-content-editor';

	#[Action('enqueue_block_editor_assets')]
	public function enqueueBlockEditorAssets(): void
	{
		$screen = get_current_screen();
		$post = get_post();

		if (null === $screen || ! $post instanceof \WP_Post) {
			return;
		}

		if (! in_array($screen->post_type, (array) config('wp-live-content.post-types', []), true)) {
			return;
		}

		if (! current_user_can('edit_post', $post->ID)) {
			return;
		}

		wp_enqueue_script(
			self::EDITOR_HANDLE,
			$this->appendToBaseUrl('/yard/live-content/assets/js/editor'),
			['wp-components', 'wp-data', 'wp-editor', 'wp-element', 'wp-plugins'],
			(string) filemtime(__DIR__ . '/../resources/scripts/editor.js'),
			true
		);

		wp_add_inline_script(
			self::EDITOR_HANDLE,
			sprintf(
				'window.yardLiveContent = %s;',
				wp_json_encode(['nonce' => wp_create_nonce(LiveContentController::NONCE_ACTION)])
			),
			'before'
		);
	}

	#[Action('wp_enqueue_scripts')]
	public function enqueueScripts(): void
	{
		wp_register_script(
			'htmx',
			$this->appendToBaseUrl('/yard/live-content/assets/js/htmx'),
			[],
			'2.0.4',
			[
				'strategy' => 'defer',
				'in_footer' => true,
			]
		);
	}
}
