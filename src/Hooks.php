<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Yard\Hook\Action;
use Yard\LiveContent\Traits\Helpers;

class Hooks
{
	use Helpers;

	#[Action('admin_bar_menu', 999)]
	public function addButtonToAdminBar(): void
	{
		global $wp_admin_bar;
		global $post;

		if (
			! is_a($post, 'WP_Post') ||
			! is_a($wp_admin_bar, 'WP_Admin_Bar')) {
			return;
		}

		// @var array $postTypes
		$postTypes = config('wp-live-content.post-types', []);

		if (in_array($post->post_type, $postTypes)) {
			$wp_admin_bar->add_menu(
				[
					'id' => 'live-content',
					'title' => 'Stuur push bericht',
					'meta' => [
						'onclick' => sprintf(
							'fetch("%s", { method: "POST" }).then(response => { if (response.ok) { alert("Push bericht verstuurd!"); } else { alert("Het sturen van een push bericht is mislukt."); } })',
							'/yard/live-content/update?id=' .  $post->ID
						),
					],
				]
			);
		}
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

		wp_register_script(
			'htmx-sse',
			$this->appendToBaseUrl('/yard/live-content/assets/js/htmx-sse'),
			['htmx'],
			'2.2.3',
			[
				'strategy' => 'defer',
				'in_footer' => true,
			]
		);
	}
}
