# Silverstripe / Vanilla Cookie Consent

[![stability-beta](https://img.shields.io/badge/stability-beta-33bbff.svg)](https://github.com/mkenney/software-guides/blob/master/STABILITY-BADGES.md#beta)

This module adds a simple cookie consent banner to your Silverstripe website. It uses the [CookieConsent v3](https://cookieconsent.orestbida.com/) script.
You can use it with the [Fluent](https://github.com/tractorcow-farm/silverstripe-fluent) translation module.

* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Custom Styling](#custom-styling)
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
Include the template like this:
```ss
 <% include CookieConsent %>
```

It will not be displayed on "Security" Pages (Login, Register, ForgotPassword, ResetPassword) and on the Admin Area.
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
  analytics_cookie_table: # Adds a table with the cookies used for analytics (Needs to match the category) OPTIONAL
    _ga: '_ga_location.hostname'
    _gat: '_gat_location.hostname'
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

## Reporting Issues

Please [create an issue](https://github.com/pixelpoems/silverstripe-vanilla-cookieconsent/issues) for any bugs you've found, or
features you're missing.

## Credits
Cookie Consent from https://cookieconsent.orestbida.com/

