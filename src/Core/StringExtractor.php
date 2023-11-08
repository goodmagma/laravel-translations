<?php

namespace Goodmagma\Translations\Core;

class StringExtractor
{
    /**
     * @var \Goodmagma\Translations\Core\FileFinder
     */
    private $finder;

    /**
     * @var \Goodmagma\Translations\Core\CodeParser
     */
    private $parser;

    /**
     * Creates a new StringExtractor instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->finder = new FileFinder();
        $this->parser = new CodeParser();
    }

    /**
     * Extract translatable strings from the project files.
     *
     * @return array
     */
    public function extract()
    {
        $strings = [];

        $files = $this->finder->find();
        foreach ($files as $file) {
            $strings = array_merge($strings, $this->parser->parse($file));
        }

        return $this->formatArray($strings);
    }

    /**
     * Convert an array of extracted strings to an associative array where each string becomes key and value.
     *
     * @param  array  $strings
     * @return array
     */
    protected function formatArray(array $strings)
    {
        $result = [];

        foreach ($strings as $string) {
            $result[$string] = $string;
        }

        return $result;
    }
}
