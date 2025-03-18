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
 <% include Pixelpoems\CookieConsent %>
```
The other option is to pass the config via the body tag like this:
```
<body data-cc-config="$CCJSConfig">
```

The consent will NOT be displayed on "Security" Pages (Login, Register, ForgotPassword, ResetPassword) and on the Admin Area.
Furthermore, you can hide it on specific pages by checking the "Hide Cookie Consent" checkbox in the CMS (Page Settings).

If you want to add an "Open Modal" button you can add this in your template:
```ss
<button id="cookieconsent__settings-btn">
    <%t VanillaCookieConsent\ConsentModal.ShowConsent 'Cookie Settings' %>
</button>
```

### JavaScript Customization
If you not want to use the default js (for example if you want to use the script in a different way or display it after you asked for the language or something else) you can disable the default js by adding the following to your project:

In your js file:
```js
import { handleCookieConsentDialog } from '../../../../../vendor/pixelpoems/silverstripe-vanilla-cookieconsent/client/dist/javascript/vanilla-cookie-consent-dialog.min.js';

document.addEventListener('DOMContentLoaded', () => {
    // Your custom code
    
    // Call the function to handle the cookie consent dialog
    handleCookieConsentDialog();
});
```

In your yml config:
```yml
VanillaCookieConsent\Services\CCService:
  disable_default_css: true # Disables the default css
  disable_default_js: true # Disables the default js
```


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
  analytics_cookie_table: # e.g. Adds a table with the cookies used for analytics (Needs to match the category) OPTIONAL
    _ga: '_ga_location.hostname'
    _gat: '_gat_location.hostname'
```

#### Example Configuration
Here are some example for the config (The Base Translations for those are already included in the module):

##### YouTube
```yml
  categories: # necessary category is added by default
    - marketing
    - analytics
  marketing_cookie_table:
    VISITOR_INFO1_LIVE: 'youtube.com (3rd party)'
    VISITOR_PRIVACY_METADATA: 'youtube.com (3rd party)'
  analytics_cookie_table:
    GPS: 'youtube.com (3rd party)'
    YSC: 'youtube.com (3rd party)'
    PREF: 'youtube.com (3rd party)'
    DEVICE_INFO: 'youtube.com (3rd party)'
    LOGIN_INFO: 'youtube.com (3rd party)'
```

##### Vimeo
```yml
  categories: # necessary category is added by default
    - analytics
  analytics_cookie_table:
    vuid: 'vimeo.com'
    sd_identity: 'vimeo.com'
    sd_client_id: 'vimeo.com'
    Player: 'vimeo.com'
    continuous_play_v3: 'vimeo.com'
```

#### Google (reduced)
```yml
  categories: # necessary category is added by default
    - analytics
  analytics_cookie_table:
    _ga: '' # Google Analytics
    _gat: '' # Google Analytics
    _gid: '' # Google Analytics
    _gcl_au: '' # Google
    _gcl_aw: '' # Google Ads
```

## Script Management
See the docs here: https://cookieconsent.orestbida.com/advanced/manage-scripts.html

## Custom Styling
[//]: # (ToDo: Add custom styling instructions)

If you want to overwrite the default styling which are loaded from the js library, you can do this by adding the following CSS to your project:
```scss
// Prefs Window
#cc-main {
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

Or have a look here: https://cookieconsent.orestbida.com/advanced/ui-customization.html

## Translations
You can add your own translations or overwrite the existing ones by adding the following to your project look into the existing translations here [translations](./lang/en.yml)

For your custom categories and cookies you can add your translations like this:
```yml
VanillaCookieConsent\Categories:
  Youtube: 'Youtube' # Title of the category with uppercase first letter
  YoutubeDescription: 'This category includes cookies from Youtube.' # Description of the category with uppercase first letter
  Youtube_Cookie_cookie1: 'Youtube Cookie' # e.g. if you use the cookie Table - use the cookie name as key with uppercase first letter | underscore | "Cookie" | underscore | "CookieName" = cookie1 like its defined in your yml config
```
For basic description and a cookie listing you can have look at the [Open Cookie Database](https://jkwakman.github.io/Open-Cookie-Database/open-cookie-database.html)

If you want to translate the texts in the modal and the first section in the preferences window you need to make sure that the Fluent Extension is added to the SiteConfig like this (the fields are already registered for translation in the module):
```yml
SilverStripe\SiteConfig\SiteConfig:
  extensions:
    - 'TractorCow\Fluent\Extension\FluentExtension'
```

## Reporting Issues

Please [create an issue](https://github.com/pixelpoems/silverstripe-vanilla-cookieconsent/issues) for any bugs you've found, or
features you're missing.

## Credits
Cookie Consent from https://cookieconsent.orestbida.com/
Cookie Descriptions from https://jkwakman.github.io/Open-Cookie-Database/open-cookie-database.html