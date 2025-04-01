<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Yard\LiveContent\AssetController;
use Yard\LiveContent\LiveContentController;

Route::prefix('yard/live-content')
	->group(function () {
		Route::controller(AssetController::class)
			->group(
				function () {
					Route::get('/assets/js/htmx', 'htmx')->name('yard.live-content.assets.htmx');
					Route::get('/assets/js/htmx-sse', 'htmxSse')->name('yard.live-content.assets.htmx-sse');
				}
			);

		Route::controller(LiveContentController::class)
			->group(function () {
				Route::post('content', 'content')->name('yard.live-content.content');
				Route::post('update', 'update')->name('yard.live-content.update');
				Route::get('stream', 'stream')->name('yard.live-content.stream');
			});
	});
