# Configuration

## Template

If you want the cookie config in consent mode include the template like this:
```ss
<% include Pixelpoems\CookieConsent %>
```

The other option is to pass the config via the body tag like this:
```html
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

## JavaScript Customization

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

## YML Configuration

You can configure the cookies via the YML config. The following options are available:

```yml
---
Name: app-cookieconsent-config
After:
  - 'pp-vanilla-cookieconsent--config'
---
VanillaCookieConsent\Services\CCService:
  default_lang: 'en' # Default language for the consent modal
  # languages: ['de', 'en'] # Required when using tractorcow/silverstripe-fluent — list all active locales as 2-letter codes
  display_on_login_option: false # Adds a "display on login pages" checkbox to SiteConfig when enabled
  enable_consent_modal: true # Show the cookie consent modal (default: true)
  enable_iframe_manager: false # Enable iframemanager for embedded content (default: false)
  disable_default_css: false # Disable the module's default CSS (default: false)
  disable_default_js: false # Disable the module's default JS (default: false)
  # IF you want to configure services:
  categories: # necessary category is added by default
    analytics: [] # no services
    video: # services listed (e.g. youtube, vimeo)
      - youtube
      - vimeo
#  # IF you do NOT want to configure services:
#  categories: # necessary category is added by default
#    - analytics
#    - video
  # If you want to configure which iframe services are allowed
  iframe_services:
    - googlemaps
    - youtube
    - vimeo
    - yumpu
  analytics_cookie_table: # Adds a cookie table for the analytics category OPTIONAL
    _ga: 'your-location.com'
    _gat: 'your-location.com'
  video_cookie_table: # Adds a cookie table for the video category OPTIONAL
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

See the [Translations](translations.md) documentation for more information on how to add or update translations.