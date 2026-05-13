# Fluent Integration

This module supports [tractorcow/silverstripe-fluent](https://github.com/tractorcow-farm/silverstripe-fluent) for multi-language setups.

## Configuration

Add the `languages` setting so the module knows which locales are active. Without it, the module falls back to `default_lang` only and ignores the current Fluent locale:

```yml
---
Name: app-cookieconsent-config
After:
  - 'pp-vanilla-cookieconsent--config'
---
VanillaCookieConsent\Services\CCService:
  languages: ['de', 'en'] # list all active locales as 2-letter codes
```

## Translatable CMS Fields

To make the modal texts (title, description, text block) translatable per locale, add the Fluent extension to `SiteConfig`. The fields are already registered for translation in the module — you just need the extension applied:

```yml
SilverStripe\SiteConfig\SiteConfig:
  extensions:
    - 'TractorCow\Fluent\Extension\FluentExtension'
```

The following SiteConfig fields are translatable:
- `DisplayOnLogin`
- `SavePeriodForInsights`
- `ModalTitle`
- `ModalDescription`
- `TextBlockTitle`
- `TextBlockDescription`

## Consent Insights per Locale

When Fluent is installed, consent insights are automatically stored with the current locale. The CMS insights view in the Cookie Consent tab shows an additional breakdown by locale alongside the global stats.

## IFrame-only Setup (no Consent Modal)

When using only the IFrame Manager without the consent modal, make sure `default_lang` and `languages` are still configured so the info strings display in the correct language:

```yml
VanillaCookieConsent\Services\CCService:
  enable_consent_modal: false
  enable_iframe_manager: true
  default_lang: 'de'
  languages: ['de', 'en']
```

Also make sure the config is passed via the body tag since the dialog template won't be included:
```html
<body data-cc-config="$CCJSConfig">
```