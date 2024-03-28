<?php

namespace Amethyst\Tests;

use Illuminate\Support\Facades\File;

abstract class Base extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        File::cleanDirectory(database_path('migrations/'));

        $this->artisan('migrate:fresh');

        $this->artisan('vendor:publish', [
            '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
            '--force'    => true,
        ]);

        $this->artisan('migrate');

        app('eloquent.mapper')->boot();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Amethyst\Providers\ActionServiceProvider::class,
            \Amethyst\Providers\FooServiceProvider::class,
        ];
    }
}
