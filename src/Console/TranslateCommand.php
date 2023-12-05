<?php

namespace Goodmagma\Translations\Console;

use Goodmagma\Translations\Core\TranslationsTranslate;
use Illuminate\Console\Command;

/**
 * Get tradable asset pairs and save it on assetpair table
 *
 * @author Denis
 */
class TranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:translate {lang}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate string using Google Translate';

    /**
     * TranslateCommand constructor.
     *
     * @param  \Goodmagma\Translations\Core\TranslationsExporter  $exporter
     * @return void
     */
    public function __construct(protected TranslationsTranslate $translator)
    {
        parent::__construct();
    }

    /**
     * Execute task
     *
     */
    public function handle()
    {
        $languages = explode(',', $this->argument('lang'));

        foreach ($languages as $language) {
            $this->translator->translate($language);

            $this->info('Translated strings have been written to the lang/' . $language . '.json file.');
        }
    }
}
