# Silverstripe / Vanilla Cookie Consent

[![stability-beta](https://img.shields.io/badge/stability-beta-33bbff.svg)](https://github.com/mkenney/software-guides/blob/master/STABILITY-BADGES.md#beta)

This module adds a simple cookie consent banner to your Silverstripe website. It uses the [CookieConsent v3](https://cookieconsent.orestbida.com/) script.
You can use it with the [Fluent](https://github.com/tractorcow-farm/silverstripe-fluent) translation module.

## Requirements

* Silverstripe CMS ^6
* Silverstripe Framework ^6

## Installation

```
composer require pixelpoems/silverstripe-vanilla-cookieconsent
```

Use Tag `^1` for Silverstripe ^4.0 - ^5.0

For Silverstripe ^6 use Tag `^2`

## Documentation

* [Configuration](docs/configuration.md) — Template setup, JavaScript customization, YML config
* [Consent Insights](docs/consent-insights.md) — Tracking consent decisions in the CMS
* [Script Management](docs/script-management.md) — Managing scripts with data attributes
* [iFrames](docs/iframes.md) — iFrameManager setup, dynamic loading, DNA Design element
* [Custom Styling](docs/custom-styling.md) — Overwriting default styles
* [Translations](docs/translations.md) — Adding or overwriting translations
* [Fluent Integration](docs/fluent.md) — Multi-language setup with tractorcow/silverstripe-fluent

## Reporting Issues

Please [create an issue](https://github.com/pixelpoems/silverstripe-vanilla-cookieconsent/issues) for any bugs you've found, or
features you're missing.

## Credits

Cookie Consent from https://cookieconsent.orestbida.com/  
Cookie Descriptions from https://jkwakman.github.io/Open-Cookie-Database/open-cookie-database.html