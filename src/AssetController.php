<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
	public function htmx(): BinaryFileResponse
	{
		return response()->file(__DIR__.'/../resources/scripts/htmx.js', ['Content-Type' => 'application/javascript']);
	}
}
