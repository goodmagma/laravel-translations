<?php

namespace Goodmagma\Translations\Core\Utils;

/**
 * Class LangUtils is responsible for reading from and writing to language files on JSON format.
 */
class LangUtils
{
    
    /**
     * The filename without extension for persistent strings.
     *
     * @var string
     */
    public const PERSISTENT_STRINGS_FILENAME = 'persistent-strings';
    
    
    /**
     * Write a string to a file.
     *
     * @param  string  $content
     * @param  string  $path
     * @return void
     */
    public static function write(string $content, string $path)
    {
        if (! file_exists(dirname($path))) {
            mkdir(dirname($path));
        }

        file_put_contents($path, $content . PHP_EOL);
    }

    /**
     * Read json file and convert it into an array of strings.
     *
     * @param  string  $path
     * @return string|bool
     */
    public static function read(string $path)
    {
        if (! file_exists($path)) {
            return false;
        }

        return file_get_contents($path);
    }

    /**
     * Read existing translation file for the chosen language.
     *
     * @param  string  $language_path
     * @return array
     */
    public static function readTranslationFile(string $language_path)
    {
        $content = self::read($language_path);

        return self::jsonDecode($content);
    }

    /**
     * Get language file path.
     *
     * @param  string  $language
     * @return string
     */
    public static function languageFilePath(string $language)
    {
        return function_exists('lang_path') ? lang_path("$language.json") : resource_path("lang/$language.json");
    }

    /**
     * Get persistence string language file path.
     *
     * @param  string  $language
     * @return string
     */
    public static function persistentStringsLanguageFilePath(string $language)
    {
        $persistentStringsFile = self::PERSISTENT_STRINGS_FILENAME . "-$language";
        return self::languageFilePath($persistentStringsFile);
    }
    
    /**
     * Convert an array/object to the properly formatted JSON string.
     *
     * @param  array  $strings
     * @return string
     */
    public static function jsonEncode(array $strings)
    {
        return json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Convert a JSON string to an array.
     *
     * @param  string  $string
     * @return array
     */
    public static function jsonDecode(string $string)
    {
        return (array) json_decode($string);
    }
}
