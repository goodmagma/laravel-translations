<?php

namespace Goodmagma\Translations;

use Goodmagma\Translations\Console\ExportCommand;
use Goodmagma\Translations\Console\TranslateCommand;
use Goodmagma\Translations\Core\TranslationsExporter;
use Goodmagma\Translations\Core\TranslationsTranslate;
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

        $this->app->singleton(ExportCommand::class, function (Application $app) {
            return new ExportCommand($app->make(TranslationsExporter::class));
        });
        $this->commands(ExportCommand::class);

        $this->app->singleton(TranslationsTranslate::class, function (Application $app) {
            return new TranslationsTranslate();
        });

        $this->app->singleton(TranslateCommand::class, function (Application $app) {
            return new TranslateCommand($app->make(TranslationsTranslate::class));
        });
        $this->commands(TranslateCommand::class);
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
            TranslationsTranslate::class,
            ExportCommand::class,
            TranslateCommand::class,
        ];
    }
}
