<?php

declare(strict_types=1);

namespace Yard\LiveContent\View\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LiveContent extends Component
{
	public function __construct(public int $postId)
	{
		$this->registerHTMX();
	}

	private function registerHTMX(): void
	{
		add_action('wp_enqueue_scripts', function (): void {
			wp_enqueue_script('htmx');
			wp_enqueue_script('htmx-sse');
		});
	}

	public function render(): View|Factory
	{
		return view('wp-live-content::components.live-content');
	}
}
