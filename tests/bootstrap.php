<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();

if (! defined('DAY_IN_SECONDS')) {
	define('DAY_IN_SECONDS', 86400);
}
