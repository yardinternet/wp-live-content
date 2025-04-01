<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LiveContentServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		$package
			->name('wp-live-content')
			->hasConfigFile()
			->hasViews();
	}

	public function packageRegistered(): void
	{
		//
	}

	public function packageBooted(): void
	{
		//
	}
}
