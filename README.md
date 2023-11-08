# Export Strings and Automatic translate via Google Translate Tools for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/goodmagma/laravel-translations.svg?style=for-the-badge)](https://packagist.org/packages/goodmagma/laravel-translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/goodmagma/laravel-translations/run-tests.yml?branch=master&label=tests&style=for-the-badge)](https://github.com/goodmagma/laravel-translations/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/goodmagma/laravel-translations/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=for-the-badge)](https://github.com/goodmagma/laravel-translations/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Twitter Follow](https://img.shields.io/badge/follow-%40danmasonmp-1DA1F2?logo=twitter&style=for-the-badge)](https://twitter.com/danmasonmp)

You can use `__('Translate me')` or `@lang('Translate me')` with translations in JSON files to translate strings.
Laravel Translation Tools is composed by two commands:

* exporter: Collect all translatable strings of an application and create corresponding translation files in JSON format
* autotranslate: Translate all the strings of a specific language and save it to the corresponding JSON file. You may define a `persistent-strings` file in order to keep some translations.


## Installation

You can install the package via composer:

```bash
composer require goodmagma/laravel-translations --dev
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="translations-config"
```

[Click here to see the contents of the config file](config/translations.php).

You should read through the config, which serves as additional documentation and make changes as needed.


## Usage

### Export translatable strings

```bash
php artisan translations:export <lang>
```

Where `<lang>` is a language code or a comma-separated list of language codes.
For example:

```bash
php artisan translations:export es
php artisan translations:export es,bg,de
```

The command with the `"es,bg,de"` parameter passed will create `es.json`, `bg.json`, `de.json` files with translatable strings or update the existing files in the `lang/` folder of your project.

### Find untranslated strings in a language file (command)

To inspect an existing language file (find untranslated strings), use this command:

```bash
php artisan translations:inspect-translations fr
```

The command only supports inspecting one language at a time.

To export translatable strings for a language and then inspect translations in it, use the following command:

```bash
php artisan translations:inspect-translations fr --export-first
```

### Find untranslated strings in a language file (IDE)

An alternative way to find untranslated strings in your language files is to search for entries with the same string for original and translated.
You can do this in most editors using a regular expression.

In PhpStorm and VSCode, you can use this pattern: `"([^"]*)": "\1"`

### Persistent strings

Some strings are not included in the export, because they are being dynamically generated. For example:

`{{ __(sprintf('Dear customer, your order has been %s', $orderStatus)) }}`

Where `$orderStatus` can be `'approved'`, `'paid'`, `'cancelled'` and so on.

In this case, you can add the strings to the `<lang>.json` file manually. For example:

```
  ...,
  "Dear customer, your order has been approved": "Dear customer, your order has been approved",
  "Dear customer, your order has been paid": "Dear customer, your order has been paid",
  ...
```

In order for those, manually added, strings not to get removed the next time you run the export command, you should add them to a json file named `persistent-strings-<lang>.json`. For example:

```
[
  ...,
  "Dear customer, your order has been %s": "",
  ...
]
```

You may also use the `persistent-strings-<lang>.json` file to fix some translations when you use autotranslate. When a translation is defined here will not be automatic translated via Google Translate. This is useful for example when the automatic translation output for a particular string is not what you want.

## License & Copyright

[MIT](LICENSE)
