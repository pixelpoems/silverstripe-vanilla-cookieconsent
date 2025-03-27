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

Include the script in your template:
```ss
<% require javascript('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/javascript/vanilla-cookie-consent-dialog.min.js') %>
```
or your PageController:
```php
Requirements::javascript('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/javascript/vanilla-cookie-consent-dialog.min.js');
```

In your js file:
```js
document.addEventListener('DOMContentLoaded', () => {
    // Your custom code
    
    // Call the function to handle the cookie consent dialog
    window.handleCookieConsentDialog();
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
  categories: # necessary category is added by default
    analytics: [] # no services
    video: # services listed (e.g. youtube, vimeo)
      - youtube
      - vimeo
  analytics_cookie_table: # e.g. Adds a table with the cookies used for analytics (Needs to match the category) OPTIONAL
    _ga: 'your-location.com'
    _gat: 'your-location.com'
  video_cookie_table:
    VISITOR_INFO1_LIVE: '.youtube.com'
    VISITOR_PRIVACY_METADATA: '.youtube.com'
    YSC: '.youtube.com'
    __cf_bm: '.vimeo.com'
    _cfuvid: '.vimeo.com'
    vuid: '.vimeo.com'
```

If you want to update or add the storage of a specific cookie in your cookie table, you have to do this via the lang yml files:
```yml
en:
  VanillaCookieConsent\Categories:
    Analytics_Cookie__ga_Storage: '2 Years'
    Analytics_Cookie__gcl_au_Storage: '90 Days'
```

The Base Translations for those are already included in the module.
If you want don't want to use the video category you can add the cookies to analytics or create a new category, but attention, then you need to add the translations for the new category and the cookies.


## Script Management

### Available script Attributes
According to orestbida.com there are the following script attributes available:

`data-category`: name of the category

`data-service` (optional): if specified, a toggle will be generated in the preferencesModal

`data-type` (optional): custom type (e.g. "module")

`data-src` (optional): can be used instead of src to avoid validation issues

Example usage:
```html
      <script
      type="text/plain"
      data-category="analytics"
      data-service="Google Analytics"
      >/*...code*/</script>
```
For further information have a look at the [Cookie Consent Documentation - Script Management](https://cookieconsent.orestbida.com/advanced/script-management.html)


## iFrames
This module comes with an iFrame solution for the video category. 

### TinyMCE Fields
WIP

[//]: # (ToDo: WIP)

### DNA Design Element
This Module comes with an Video Element for DNA Design. If you want to overwrite the template just copy the file from the module to your theme and adjust it to your needs (`templates > DNADesign > Elemental > Layout > VideoElement.ss`)
Within this element a video can be added via youtube, viemo or a simple upload.

### Vanilla Usage
If you want to use the iFrame solution without the DNA Design Element you can use the following code:
```html
<!-- Works for youtube and vimeo, just add the name of the service and the id -->
 <div data-service="youtube|vimeo" data-id="add-your-id" data-autoscale></div>
```

For further information have a look at the [Cookie Consent Documentation - iFrameManager](https://cookieconsent.orestbida.com/advanced/iframemanager-setup.html)


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

## ToDos
- [ ] Add TinyMCE Field for iFrame
- [ ] Check if Element does not course any issues without DNA Design Module installed
- [ ] Add custom styling instructions
- [ ] iFrame Manager without cookie consent dialog