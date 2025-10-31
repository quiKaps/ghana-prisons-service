<?php

namespace App\Providers;

use League\Flysystem\Filesystem as Flysystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Illuminate\Filesystem\FilesystemAdapter;


class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient(
                $config['authorization_token']
            );

            $adapter = new DropboxAdapter($client);
            $flysystem = new Flysystem($adapter);

            return new FilesystemAdapter($flysystem, $adapter);
        });
    }
}
