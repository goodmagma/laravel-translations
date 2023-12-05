<?php

namespace Goodmagma\Translations\Core;

use Goodmagma\Translations\Core\Utils\LangUtils;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationsTranslate extends TranslationsExporter
{
    /**
     * Extractor object.
     *
     * @var \Stichoza\GoogleTranslate\GoogleTranslate
     */
    private $gtranslate;

    /**
     * Parser constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->gtranslate = new GoogleTranslate();
    }

    /**
     * Export translatable strings to the language file.
     *
     * @param  string  $language
     * @return void
     */
    public function translate(string $language)
    {
        $this->gtranslate->setTarget($language);

        $language_path = LangUtils::languageFilePath($language);

        // Read existing translation file for the chosen language.
        $existing_strings = LangUtils::readTranslationFile($language_path);

        // Get the persistent strings.
        $persistent_strings_path = LangUtils::persistentStringsLanguageFilePath($language);
        $persistent_strings = LangUtils::readTranslationFile($persistent_strings_path);

        //translate existing strings
        $translated_strings = [];
        foreach ($existing_strings as $key => $value) {
            //already translated?
            if($key == $value) {
                $text = $this->gtranslate->translate($key);

                $translated_strings[$key] = $text;
            }
        }

        // Merge old an new translations preserving existing translations and persistent strings.
        $resulting_strings = array_merge($existing_strings, $translated_strings);
        $resulting_strings = array_merge($resulting_strings, $persistent_strings);

        // Wisely sort the translations if enabled through the config.
        $sorted_strings = $this->advancedSort($resulting_strings);

        // Prepare JSON string and dump it to the translation file.
        $content = LangUtils::jsonEncode($sorted_strings);
        LangUtils::write($content, $language_path);
    }
}
