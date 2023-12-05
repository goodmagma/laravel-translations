<?php

namespace Goodmagma\Translations;

use Goodmagma\Translations\Console\ExportTranslationsCommand;
use Goodmagma\Translations\Core\TranslationsExporter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TranslationsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/translations.php' => config_path('translations.php'),
        ], 'translations-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TranslationsExporter::class, function (Application $app) {
            return new TranslationsExporter();
        });

        $this->app->singleton(ExportTranslationsCommand::class, function (Application $app) {
            return new ExportTranslationsCommand($app->make(TranslationsExporter::class));
        });
        $this->commands(ExportTranslationsCommand::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            TranslationsExporter::class,
            ExportTranslationsCommand::class,
        ];
    }
}
