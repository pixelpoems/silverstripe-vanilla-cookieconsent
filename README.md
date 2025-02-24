# Silverstripe / Vanilla Cookie Consent

[![stability-beta](https://img.shields.io/badge/stability-beta-33bbff.svg)](https://github.com/mkenney/software-guides/blob/master/STABILITY-BADGES.md#beta)

This module adds a simple cookie consent banner to your Silverstripe website. It uses the [CookieConsent v3](https://cookieconsent.orestbida.com/) script.
You can use it with the [Fluent](https://github.com/tractorcow-farm/silverstripe-fluent) translation module.

* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Custom Styling](#custom-styling)
* [Translations](#translations)
* [Reporting Issues](#reporting-issues)
* [Credits](#credits)

## Requirements

* Silverstripe CMS >=4.0
* Silverstripe Framework >=4.0
* Versioned Admin >=1.0

## Installation
```
composer require pixelpoems/silverstripe-vanilla-cookieconsent
```

## Configuration
### Template
If you want the cookie config in consent mode include the template like this:
```ss
 <% include CookieConsent %>
```
The other option is to pass the config via the body tag like this:
```
<body data-cc-config="$CCJSConfig">
```

The consent will NOT be displayed on "Security" Pages (Login, Register, ForgotPassword, ResetPassword) and on the Admin Area.
Furthermore, you can hide it on specific pages by checking the "Hide Cookie Consent" checkbox in the CMS (Page Settings).

### YML Configuration
You can configure the cookies via the YML config. The following options are available:

```yml
VanillaCookieConsent\Services\CCService:
  default_lang: 'de' # Default language
  languages: # Only used if you want to use multiple languages in combination with fluent otherwise only default_lang is necessary
    - de
    - en
  categories: # necessary category is added by default
    - analytics
    - marketing
    - youtube
  analytics_cookie_table: # e.g. Adds a table with the cookies used for analytics (Needs to match the category) OPTIONAL
    _ga: '_ga_location.hostname'
    _gat: '_gat_location.hostname'
  youtube_cookie_table: # e.g. Adds a table with the cookies used for youtube (Needs to match the category) OPTIONAL
    cookie1: 'cookie1_location.hostname'
```


## Custom Styling
[//]: # (ToDo: Add custom styling instructions)

If you want to overwrite the default styling which are loaded from the js library, you can do this by adding the following CSS to your project:
```scss
// Prefs Window
#cc-main#cc-main { // You need to double the id to override the default styles which are loaded via module
	--cc-btn-border-radius: 0px;
	--cc-btn-primary-bg: var(--color-primary);
	--cc-btn-secondary-bg: var(--color-secondary);
	font-family: var(--font-base);
    
	.pm--box{
		border-radius: 0;
	}
    
	.pm__header{
		.pm__title{
			font-size: var(--fs-md);
		}
	}
    
	.pm__body{
		.pm__section-title{
			font-size: var(--fs-sm);
		}
	}
    
	.pm__btn {
		border: 0px;
		padding: var(--btn-padding-y) var(--btn-padding-x);
		font-size: var(--btn-font-size);
	}
    
	.pm__footer{}
}
```

## Translations
You can add your own translations or overwrite the existing ones by adding the following to your project look into the existing translations here [translations](./lang/en.yml)

For your custom categories and cookies you can add your translations like this:
```yml
VanillaCookieConsent\Categories:
  Youtube: 'Youtube' # Title of the category with uppercase first letter
  YoutubeDescription: 'This category includes cookies from Youtube.' # Description of the category with uppercase first letter
  Youtube_Cookie_cookie1: 'Youtube Cookie' # e.g. if you use the cookie Table - use the cookie name as key with uppercase first letter | underscore | "Cookie" | underscore | "CookieName" = cookie1 like its defined in your yml config
```

## Reporting Issues

Please [create an issue](https://github.com/pixelpoems/silverstripe-vanilla-cookieconsent/issues) for any bugs you've found, or
features you're missing.

## Credits
Cookie Consent from https://cookieconsent.orestbida.com/




https://jkwakman.github.io/Open-Cookie-Database/open-cookie-database.html