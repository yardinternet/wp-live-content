<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Yard\Data\PostData;

// Assets
Route::get('/yard/live-content/assets/js/htmx', function () {
	return response()->file(__DIR__.'/../resources/scripts/htmx.js', ['Content-Type' => 'application/javascript']);
});

Route::get('/yard/live-content/assets/js/htmx-sse', function () {
	return response()->file(__DIR__.'/../resources/scripts/sse.js', ['Content-Type' => 'application/javascript']);
});

// Live content
Route::post('/live-content', function (Request $request) {
	$postId = $request->query('id');
	$postData = PostData::from(get_post($postId));

	return view('wp-live-content::components.live-content', ['postData' => $postData]);
});

Route::post('/update-post', function (Request $request) {
	$postId = $request->query('id');

	set_transient('post_updated_' . $postId, 1);

	return response()->json(['message' => 'Post with id ' . $postId . ' has been updated']);
});

function sendEvent(string $event, string $data): void
{
	echo "event: $event\n",
	'data: ' . $data , "\n\n";

	while (ob_get_level() > 0) {
		ob_end_flush();
	}

	flush();
}

Route::get('/sse', function (Request $request) {
	$postId = $request->query('id');

	if (empty($postId)) {
		return response()->json(['message' => 'Post id is required'], 400);
	}

	header('X-Accel-Buffering: no');
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');

	sendEvent('info', 'Connection established for post ' . $postId);

	while (1 != get_transient('post_updated_' . $postId)) {
		if (connection_aborted()) {
			break;
		}

		wp_cache_flush();

		sendEvent('info', 'Waiting for post update with id ' . $postId);

		sleep(2);
	}

	delete_transient('post_updated_' . $postId);

	$postData = PostData::from(get_post($postId));

	$view = view('wp-live-content::partials.notification', ['postData' => $postData]);

	$html = str_replace(["\n", "\t"], '', $view->render());

	sendEvent('update', $html);

	sendEvent('close', 'connection closed');
});
