<?php

declare(strict_types=1);

namespace Yard\LiveContent\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Yard\Data\PostData;

class LiveContent extends Component
{
	public function __construct(public PostData $postData)
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

	public function render(): View|Closure|string
	{
		return view('wp-live-content::components.live-content');
	}
}
