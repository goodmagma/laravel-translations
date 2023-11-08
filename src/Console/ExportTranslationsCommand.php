<?php

namespace Goodmagma\Translations\Console;

use Goodmagma\Translations\Core\TranslationExporter;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ExportTranslationsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translatable strings for a language to a JSON file.';

    /**
     * ExportCommand constructor.
     *
     * @param  \Goodmagma\TranslationTools\Core\TranslationExporter  $exporter
     * @return void
     */
    public function __construct(protected TranslationExporter $exporter)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $languages = explode(',', $this->argument('lang'));

        foreach ($languages as $language) {
            $this->exporter->export($language);

            $this->info('Translatable strings have been extracted and written to the ' . $language . '.json file.');
        }

        return static::SUCCESS;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'lang',
                InputArgument::REQUIRED,
                'A language code or a comma-separated list of language codes for which the translatable strings are extracted, e.g. "es" or "es,bg,de".',
            ],
        ];
    }
}
