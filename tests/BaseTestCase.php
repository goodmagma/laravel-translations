<?php

namespace Tests;

use Goodmagma\Translations\TranslationsServiceProvider;
use Orchestra\Testbench\TestCase;

class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TranslationsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . '__fixtures');
        $app['config']->set('translations.directories', [
            'resources',
        ]);

        $app['config']->set('translations.excluded-directories', [
            'views/ignored',
        ]);

        $app['config']->set('translations.sort-keys', true);

        $app['config']->set('translations.functions', [
            '__',
            '_t',
            '@lang',
        ]);
    }

    protected function removeJsonLanguageFiles()
    {
        $path = $this->getTranslationFilePath('*');
        $files = glob($path); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    protected function createTestView($content)
    {
        file_put_contents(resource_path('views/index.blade.php'), $content);
    }

    protected function getTranslationFilePath($language)
    {
        return function_exists('lang_path') ? lang_path("$language.json") : resource_path("lang/$language.json");
    }

    protected function getTranslationFileContent($language)
    {
        $path = $this->getTranslationFilePath($language);

        $content = file_get_contents($path);

        return json_decode($content, true);
    }

    protected function writeToTranslationFile($language, $content)
    {
        $path = $this->getTranslationFilePath($language);
        file_put_contents($path, $content);
    }
}
