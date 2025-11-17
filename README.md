# Silverstripe / Vanilla Cookie Consent

[![stability-beta](https://img.shields.io/badge/stability-beta-33bbff.svg)](https://github.com/mkenney/software-guides/blob/master/STABILITY-BADGES.md#beta)

This module adds a simple cookie consent banner to your Silverstripe website. It uses the [CookieConsent v3](https://cookieconsent.orestbida.com/) script.
You can use it with the [Fluent](https://github.com/tractorcow-farm/silverstripe-fluent) translation module.

* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Consent Insights](#consent-insights)
* [iFrames](#iframes)
* [Custom Styling](#custom-styling)
* [Translations](#translations)
* [Reporting Issues](#reporting-issues)
* [Credits](#credits)

## Requirements

* Silverstripe CMS ^6
* Silverstripe Framework ^6

## Installation
```
composer require pixelpoems/silverstripe-vanilla-cookieconsent
```

Use Tag `^1` for Silverstripe ^4.0 - ^5.0

For Silverstripe ^6 use Tag `^2`

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
If you do not want to use the default js (for example if you want to use the script in a different way or display it after you asked for the language or something else) you can disable the default js by adding the following to your project:

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
  display_on_login_option: false # If you want to display the cookie consent on login pages enable this to add a checkbox to the siteconfig settings
  # IF you want to configure services:
  categories: # necessary category is added by default
    analytics: [] # no services
    video: # services listed (e.g. youtube, vimeo)
      - youtube
      - vimeo
#  # IF you NOT want to configure services:
#  categories: # necessary category is added by default
#    - analytics
#    - video
  # If you want to configure which iframe services are allowed
  iframe_services:
    - googlemaps
    - youtube
    - vimeo
    - yumpu
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

The base translations for those are already included in the module.
If you don't want to use the video category you can add the cookies to analytics or create a new category, but attention, then you need to add the translations for the new category and the cookies.

## Consent Insights

This module includes a built-in analytics feature that tracks user consent decisions and displays visual insights in the CMS.

### Enabling Insights

To enable consent insights, configure the save period in your SiteConfig settings (Settings > Cookie Consent):

1. Navigate to the CMS Settings area
2. Go to the "Cookie Consent" tab
3. Set the "Save Period for Insights (in days)" field to the number of days you want to track (e.g., 30 or 90)
4. Set to 0 to disable insights entirely

### What Gets Tracked

When enabled, the module tracks:
- **Timestamp**: When the consent was given
- **Consent Type**: Whether the user accepted all, rejected all, or partially accepted categories
- **Accepted Categories**: Which specific cookie categories were accepted

### Viewing Insights

Once configured, the Cookie Consent settings tab displays:
- **Visual Charts**: Accept/Reject rate pie chart and category-specific acceptance rates
- **Data Grid**: Detailed list of all consent records from the specified period
- **Locale-specific data**: If using Fluent, insights are broken down by locale

The insights automatically respect Subsites and Fluent locales if those modules are installed.

### Data Management

Old insights are automatically cleaned up based on your configured save period. You can manually clear insights by running:

```bash
sake dev/tasks/VanillaCookieConsent-Tasks-ClearConsentInsightsTask
```

This task will:
- Remove insights older than the configured save period
- Delete all insights if the save period is set to 0 or not configured

**Important**: Consent insights are stored in the database. Make sure you comply with your privacy policy and GDPR requirements when collecting this data.

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

If you want to use the iFrame solution you need to add the following to your yml config:
```yml
VanillaCookieConsent\Services\CCService:
  enable_iframe_manager: true
```

### TinyMCE Fields
If you add an embed in your TinyMCE for the DBHTMLTexts there is an Extension which preps the iframe for the usage of the iframemanager script. In your template just call:
`$HTML.Embedded` - this will update the html so that the iframemanager can do the rest automatically.

### DNA Design Element
This Module comes with an Video Element for DNA Design. If you want to overwrite the template just copy the file from the module to your theme and adjust it to your needs (`templates > DNADesign > Elemental > Layout > VideoElement.ss`)
Within this element a video can be added via youtube, vimeo, yumpu or a simple upload.

You can edit the allowed embeddable services in the config:
```yml
VanillaCookieConsent\Services\CCService:
  iframe_services:
    - youtube
    - vimeo
    - yumpu
```

Also add a video category to your consent categories so the consent can handle the services:
```yml
VanillaCookieConsent\Services\CCService:
  video: # Category for video services
    - youtube # make sure it's written like the services above
    - vimeo
```

So your full config for the iframe looks like this:
```yml
VanillaCookieConsent\Services\CCService:
  enable_iframe_manager: true
  iframe_services:
    - youtube
    - vimeo
  categories: # necessary category is added by default
    video: # Category for video services
      - youtube
      - vimeo
```

### Vanilla Usage
If you want to use the iFrame solution without the DNA Design Element you can use the following code:
```html
<!-- Works for youtube and vimeo, just add the name of the service and the id -->
 <div data-service="youtube|vimeo" data-id="add-your-id" data-autoscale></div>
```

### Only IFrameManager (without Cookie Consent Modal)
If you want to use the IFrame without the cookie Consent you can disable the modal like this in your yml:
```yml
# For this it's important to load after the module config 'pp-vanilla-cookieconsent--config'
---
Name: app-cookieconsent-config
After:
  - 'pp-vanilla-cookieconsent--config'
---
VanillaCookieConsent\Services\CCService:
  enable_consent_modal: false
```
**Attention**: Make sure that the `default_lang` and the languages are still defined so that the info strings are displayed in the correct language. Furthermore if no categories or services are defined - all of the possibilities are added to the iframe manager if this is enabled. Furthermore make sure you add the CCConfig to your body.
```html
<body data-cc-config="$CCJSConfig">
```

For further information have a look at the [Cookie Consent Documentation - iFrameManager](https://cookieconsent.orestbida.com/advanced/iframemanager-setup.html)


## Custom Styling

If you want to overwrite the default styling which is loaded from the js library, you can do this by adding the following CSS to your project:
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
