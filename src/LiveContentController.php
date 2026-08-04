<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Webmozart\Assert\Assert;

class LiveContentController extends Controller
{
	public function content(Request $request): View|Factory
	{
		$postId = $request->query('id');
		
		return view('wp-live-content::components.live-content', ['postId' => $postId]);
	}

	public function update(Request $request): JsonResponse
	{
		$postId = filter_var($request->query('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

		if (false === $postId) {
			return response()->json(['message' => 'Post id must be a positive integer'], 400);
		}

		if (! current_user_can('edit_post', $postId)) {
			return response()->json(['message' => 'You are not allowed to push updates for this post'], 403);
		}

		set_transient('post_updated_' . $postId, time(), DAY_IN_SECONDS);

		return response()->json(['message' => 'Post with id ' . $postId . ' has been updated']);
	}

	/**
	 * @throws \Throwable
	 */
	public function poll(Request $request): Response
	{
		$postId = filter_var($request->query('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		$since = filter_var($request->query('since'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

		if (false === $postId || false === $since) {
			return $this->pollResponse('', 400);
		}

		$post = get_post($postId);

		if (null === $post || ! is_a($post, 'WP_Post')) {
			return $this->pollResponse('', 404);
		}

		$pushedAt = (int) get_transient('post_updated_' . $postId);

		if ($since >= $pushedAt) {
			return $this->pollResponse('', 204);
		}

		$view = view('wp-live-content::partials.notification', ['postId' => $postId]);

		Assert::isInstanceOf($view, View::class);

		return $this->pollResponse($view->render(), 200);
	}

	private function pollResponse(string $content, int $status): Response
	{
		return response($content, $status, ['Cache-Control' => 'no-store']);
	}
}
