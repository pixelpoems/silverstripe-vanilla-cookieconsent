# Translations

You can add your own translations or overwrite the existing ones by adding the following to your project. Look into the existing translations here: [translations](../lang/en.yml)

For your custom categories and cookies you can add your translations like this:
```yml
VanillaCookieConsent\Categories:
  Youtube: 'Youtube' # Title of the category with uppercase first letter
  YoutubeDescription: 'This category includes cookies from Youtube.' # Description of the category with uppercase first letter
  Youtube_Cookie_cookie1: 'Youtube Cookie' # e.g. if you use the cookie Table - use the cookie name as key with uppercase first letter | underscore | "Cookie" | underscore | "CookieName" = cookie1 like its defined in your yml config
```

For basic descriptions and a cookie listing you can have a look at the [Open Cookie Database](https://jkwakman.github.io/Open-Cookie-Database/open-cookie-database.html)

If you want to translate the texts in the modal and the first section in the preferences window you need to make sure that the Fluent Extension is added to the SiteConfig like this (the fields are already registered for translation in the module):
```yml
SilverStripe\SiteConfig\SiteConfig:
  extensions:
    - 'TractorCow\Fluent\Extension\FluentExtension'
```

When using Fluent, you also need to configure the `languages` setting so the module knows which locales are active. Without this, the module falls back to `default_lang` only and ignores the current Fluent locale:
```yml
VanillaCookieConsent\Services\CCService:
  languages: ['de', 'en'] # list all active locales as 2-letter codes
```