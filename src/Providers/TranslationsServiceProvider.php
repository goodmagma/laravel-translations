<?php

namespace Goodmagma\Translations\Providers;

use Goodmagma\Translations\Console\CheckTranslationsCommand;
use Goodmagma\Translations\Console\ExportTranslationsCommand;
use Goodmagma\Translations\Core\TranslationExporter;
use Goodmagma\Translations\Core\UntranslatedStringFinder;
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

        $this->app->singleton(TranslationExporter::class, function (Application $app) {
            return new TranslationExporter();
        });

        $this->app->singleton(ExportTranslationsCommand::class, function (Application $app) {
            return new ExportTranslationsCommand($app->make(TranslationExporter::class));
        });
        $this->commands(ExportTranslationsCommand::class);



        $this->app->singleton(UntranslatedStringFinder::class, function (Application $app) {
            return new UntranslatedStringFinder();
        });

        $this->app->singleton(CheckTranslationsCommand::class, function ($app) {
            return new CheckTranslationsCommand(
                $app->make(TranslationExporter::class),
                $app->make(UntranslatedStringFinder::class)
            );
        });
        $this->commands(CheckTranslationsCommand::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            TranslationExporter::class,
            ExportTranslationsCommand::class,
            UntranslatedStringFinder::class,
            CheckTranslationsCommand::class,
        ];
    }
}
