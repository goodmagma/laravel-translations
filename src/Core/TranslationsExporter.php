<?php

namespace Goodmagma\Translations\Core;

use Goodmagma\Translations\Core\Utils\LangUtils;
use Illuminate\Support\Arr;

class TranslationsExporter
{
    /**
     * Extractor object.
     *
     * @var \Goodmagma\Translations\Core\StringExtractor
     */
    private $extractor;

    /**
     * Parser constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->extractor = new StringExtractor();
    }

    /**
     * Export translatable strings to the language file.
     *
     * @param  string  $language
     * @return void
     */
    public function export(string $language)
    {
        $language_path = LangUtils::languageFilePath($language);

        // Extract source strings from the project directories.
        $new_strings = $this->extractor->extract();

        // Read existing translation file for the chosen language.
        $existing_strings = LangUtils::readTranslationFile($language_path);

        // Get the persistent strings.
        $persistent_strings_path = LangUtils::persistentStringsLanguageFilePath($language);
        $persistent_strings = LangUtils::readTranslationFile($persistent_strings_path);

        // Add persistent strings to the export if enabled.
        //$new_strings = $this->addPersistentStrings($new_strings, $persistent_strings);

        // Merge old an new translations preserving existing translations and persistent strings.
        $resulting_strings = $this->mergeStrings($new_strings, $existing_strings, $persistent_strings);

        // Sort the translations if enabled through the config.
        $sorted_strings = $this->advancedSort($resulting_strings);

        // Prepare JSON string and dump it to the translation file.
        $content = LangUtils::jsonEncode($sorted_strings);
        LangUtils::write($content, $language_path);
    }

    /**
     * Merge two arrays of translations preserving existing translations and persistent strings.
     *
     * @param  array  $existing_strings
     * @param  array  $new_strings
     * @param  array  $persistent_strings
     * @return array
     */
    protected function mergeStrings(array $new_strings, array $existing_strings, array $persistent_strings)
    {
        $merged_strings = array_merge($new_strings, $existing_strings);
        $merged_strings = array_merge($merged_strings, $persistent_strings);

        return $merged_strings;
    }

    /**
     * Sort the translation strings alphabetically by their original strings (keys)
     * if the corresponding option is enabled through the package config.
     *
     * @param  array  $strings
     * @return array
     */
    protected function sortStrings(array $strings)
    {
        if (config('translations.sort-keys', false)) {
            return Arr::sort($strings, function ($value, $key) {
                return strtolower($key);
            });
        }

        return $strings;
    }

    /**
     * Add keys from the persistent-strings file to new strings array.
     *
     * @param  array  $new_strings
     * @param  array  $persistent_strings
     * @return array
     */
    protected function addPersistentStrings(array $new_strings, array $persistent_strings)
    {
        $new_strings = array_merge(array_combine($persistent_strings, $persistent_strings), $new_strings);

        return $new_strings;
    }

    /**
     * Wisely sort translatable strings if this option is enabled through the config.
     * If it's requested (through the config) to put untranslated strings
     * at the top of the translation file, then untranslated and translated strings
     * are sorted separately.
     *
     * @param  array  $translatable_strings
     * @return array
     */
    protected function advancedSort(array $translatable_strings)
    {
        // If it's necessary to put untranslated strings at the top.
        if (config('translations.untranslated-strings-at-the-top', false)) {
            $translated = [];
            $untranslated = [];
            foreach ($translatable_strings as $key => $value) {
                if ($key === $value) {
                    $untranslated[$key] = $value;

                    continue;
                }
                $translated[$key] = $value;
            }

            $translated = $this->sortStrings($translated);
            $untranslated = $this->sortStrings($untranslated);

            return array_merge($untranslated, $translated);
        }

        // Sort the translations if enabled through the config.
        return $this->sortStrings($translatable_strings);
    }
}
