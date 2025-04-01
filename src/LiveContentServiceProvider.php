<?php

declare(strict_types=1);

namespace Yard\LiveContent;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Yard\Hook\Registrar;
use Yard\LiveContent\View\Components\LiveContent;
use Yard\OWC\Components\InternalData;
use Yard\OWC\Components\PdcIcon;

class LiveContentServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		$package
			->name('wp-live-content')
			->hasConfigFile()
			->hasViews()
            ->hasRoute('web')
            ->hasViewComponent('yard', LiveContent::class);
	}

	public function packageRegistered(): void
	{
		//
	}

    /**
     * @throws \ReflectionException
     */
    public function packageBooted(): void
	{
        (new Registrar([
                Hooks::class
        ]))->registerHooks();
	}
}
