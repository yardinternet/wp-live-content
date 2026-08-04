<?php

declare(strict_types=1);

it('renders a polling element', function () {
	\WP_Mock::userFunction('get_the_content', [
		'return' => 'post content',
	]);

	$html = view('wp-live-content::components.live-content', ['postId' => 5])->render();

	expect($html)
		->toContain('hx-get="/yard/live-content/poll?id=5&since=')
		->toContain('hx-trigger="every 10s"')
		->toContain('hx-swap="outerHTML"')
		->toContain('post content');
});
