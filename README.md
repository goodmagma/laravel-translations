# Export Strings and Automatic translate via Google Translate Tools for Laravel

[![Tests Status Badge](https://github.com/goodmagma/laravel-translations/actions/workflows/run-tests.yml/badge.svg)](https://github.com/goodmagma/laravel-translations/actions/workflows/run-tests.yml)
[![PHPStan Status Badge](https://github.com/goodmagma/laravel-translations/actions/workflows/phpstan.yml.yml/badge.svg)](https://github.com/goodmagma/laravel-translations/actions/workflows/phpstan.yml.yml)
[![Code Styles Check Badge](https://github.com/goodmagma/laravel-translations/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/goodmagma/laravel-translations/actions/workflows/php-cs-fixer.yml)

You can use `__('Translate me')` or `@lang('Translate me')` with translations in JSON files to translate strings.
Laravel Translation Tools is composed by two commands:

* exporter: Collect all translatable strings of an application and create corresponding translation files in JSON format
* autotranslate: Translate all the strings of a specific language and save it to the corresponding JSON file. You may define a `persistent-strings` file in order to keep some translations.


## Attribution

This project includes code from the following open-source projects:

**Translatable String Exporter for Laravel**
- Repository: [https://github.com/kkomelin/laravel-translatable-string-exporter](https://github.com/kkomelin/laravel-translatable-string-exporter)
- License: [MIT License](https://github.com/kkomelin/laravel-translatable-string-exporter/blob/master/LICENSE)


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


### Translate strings

```bash
php artisan translations:translate <lang>
```

Where `<lang>` is a language code or a comma-separated list of language codes.
For example:

```bash
php artisan translations:translate es
php artisan translations:translate es,bg,de
```


### Persistent Strings

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
  "Dear customer, your order has been %s": "Gentile cliente, il tuo ordine è stato approvato",
  ...
]
```

You may also use the `persistent-strings-<lang>.json` file to fix some translations when you use translate command. 
When a translation is defined here will not be automatic translated via Google Translate. This is useful for example when the automatic translation output for a particular string is not accurate.


## Contributing

Contributions are what makes the open source community such an amazing place to learn, inspire and create. Any
contributions you make are **greatly appreciated**.

- Give us a star :star:
- Fork and Clone! Awesome
- Select existing [issues](https://github.com/goodmagma/laravel-translations/issues) or create a [new issue](https://github.com/goodmagma/laravel-translations/issues/new) and give us a PR with your bugfix or improvement after. We love it ❤️

If you want to make a PR:

1. Fork the Project and checkout `develop` branch
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## License

Distributed under the MIT License. See [LICENSE](LICENSE) for more information.
