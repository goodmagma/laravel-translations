<?php

namespace Goodmagma\Translations\Core;

use Goodmagma\Translations\Core\Utils\LangUtils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

class TranslationsExporter
{
    /**
     * The filename without extension for persistent strings.
     *
     * @var string
     */
    public const PERSISTENT_STRINGS_FILENAME = 'persistent-strings';

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
        $persistent_strings_path = LangUtils::languageFilePath(self::PERSISTENT_STRINGS_FILENAME);
        $persistent_strings = LangUtils::readTranslationFile($persistent_strings_path);

        // Add persistent strings to the export if enabled.
        $new_strings = $this->addPersistentStrings($new_strings, $persistent_strings);

        // Merge old an new translations preserving existing translations and persistent strings.
        $resulting_strings = $this->mergeStrings($new_strings, $existing_strings, $persistent_strings);

        // Exclude translation keys if enabled through the config.
        $resulting_strings = $this->excludeTranslationKeys($resulting_strings, $language);

        // Sort the translations if enabled through the config.
        $sorted_strings = $this->advancedSortIfEnabled($resulting_strings);

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

        return $this->arrayFilterByKey($merged_strings, function ($key) use ($persistent_strings, $new_strings) {
            return in_array($key, $persistent_strings) || array_key_exists($key, $new_strings);
        });
    }

    /**
     * Sort the translation strings alphabetically by their original strings (keys)
     * if the corresponding option is enabled through the package config.
     *
     * @param  array  $strings
     * @return array
     */
    protected function sortIfEnabled(array $strings)
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
     * Exclude Laravel translation keys from the array
     * if they have corresponding translations in the given language.
     *
     * @param  array  $translatable_strings
     * @param  string  $language
     * @return array|mixed
     */
    protected function excludeTranslationKeys(array $translatable_strings, string $language)
    {
        if (config('translations.exclude-translation-keys', false)) {
            foreach ($translatable_strings as $key => $value) {
                if ($this->isTranslationKey($key, $language)) {
                    unset($translatable_strings[$key]);
                }
            }
        }

        return $translatable_strings;
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
    protected function advancedSortIfEnabled(array $translatable_strings)
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

            $translated = $this->sortIfEnabled($translated);
            $untranslated = $this->sortIfEnabled($untranslated);

            return array_merge($untranslated, $translated);
        }

        // Sort the translations if enabled through the config.
        return $this->sortIfEnabled($translatable_strings);
    }

    /**
     * Filtering an array by its keys using a callback.
     *
     * @param  array  $array
     *  The array to iterate over.
     * @param  callable  $callback
     *  The callback function to use.
     *
     * @return array
     *  The filtered array.
     */
    private function arrayFilterByKey($array, $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Check if the given translatable string is a translation key and has a translation.
     * The translation keys are ignored if the corresponding option is set through the config.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return bool
     */
    private function isTranslationKey(string $key, string $locale)
    {
        $dot_position = strpos($key, '.');

        // Ignore string without dots.
        if ($dot_position === false) {
            return false;
        }

        // Ignore strings where the dot is at the end of a string
        // because it's a normal sentence.
        if ($dot_position === (strlen($key) - 1)) {
            return false;
        }

        $segments = explode('.', $key);

        // Everything but last segment determines a group.

        $key = array_pop($segments);
        $group = implode('.', $segments);

        $translations = Lang::get($group, [], $locale);

        // If the received translation is an array, the initial translation key is not full,
        // so we consider it wrong.

        return isset($translations[$key]) && ! is_array($translations[$key]);
    }
}
