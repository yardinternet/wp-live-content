<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiveContentController extends Controller
{
	public function content(Request $request): View|Factory
	{
		$postId = $request->query('id');
		
		return view('wp-live-content::components.live-content', ['postId' => $postId]);
	}

	public function update(Request $request): JsonResponse
	{
		$postId = $request->query('id');

		if (! is_numeric($postId)) {
			return response()->json(['message' => 'Post id must be an integer'], 400);
		}

		set_transient('post_updated_' . $postId, 1);

		return response()->json(['message' => 'Post with id ' . $postId . ' has been updated']);
	}

	/**
	 * @throws \Throwable
	 */
	public function stream(Request $request): void
	{
		header('X-Accel-Buffering: no');
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');

		$postId = (int) $request->query('id');

		if (empty($postId)) {
			$this->sendEvent('error', 'Post id is required');
			$this->sendEvent('close', 'connection closed');
			exit;
		}

		$post = get_post($postId);

		if (null === $post || ! is_a($post, 'WP_Post')) {
			$this->sendEvent('error', 'Post not found');
			$this->sendEvent('close', 'connection closed');
			exit;
		}

		$this->sendEvent('info', 'Connection established for post ' . $postId);

		while (1 != get_transient('post_updated_' . $postId)) {
			if (connection_aborted()) {
				break;
			}

			wp_cache_flush();

			$this->sendEvent('info', 'Waiting for post update with id ' . $postId);

			sleep(2);
		}

		delete_transient('post_updated_' . $postId);

		$view = view('wp-live-content::partials.notification', ['postId' => $postId]);

		if (! $view instanceof View) {
			$this->sendEvent('error', 'Error rendering view');
			$this->sendEvent('close', 'connection closed');
			exit;
		}

		$html = str_replace(["\n", "\t"], '', $view->render());

		$this->sendEvent('update', $html);

		$this->sendEvent('close', 'connection closed');
	}

	private function sendEvent(string $event, string $data): void
	{
		echo "event: $event\n",
		'data: ' . $data , "\n\n";

		while (ob_get_level() > 0) {
			ob_end_flush();
		}

		flush();
	}
}
