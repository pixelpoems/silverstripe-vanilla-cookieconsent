# iFrames

This module comes with an iFrame solution for the video category.

If you want to use the iFrame solution you need to add the following to your yml config:
```yml
VanillaCookieConsent\Services\CCService:
  enable_iframe_manager: true
```

## TinyMCE Fields

If you add an embed in your TinyMCE for the DBHTMLTexts there is an Extension which preps the iframe for the usage of the iframemanager script. In your template just call:
`$HTML.Embedded` - this will update the HTML so that the iframemanager can do the rest automatically.

## DNA Design Element

This module comes with a Video Element for DNA Design. If you want to overwrite the template just copy the file from the module to your theme and adjust it to your needs (`templates > DNADesign > Elemental > Layout > VideoElement.ss`)
Within this element a video can be added via youtube, vimeo, yumpu or a simple upload.

You can edit the allowed embeddable services in the config:
```yml
VanillaCookieConsent\Services\CCService:
  iframe_services:
    - youtube
    - vimeo
    - yumpu
    - googlemaps
```

Also add a video category to your consent categories so the consent can handle the services:
```yml
VanillaCookieConsent\Services\CCService:
  categories:
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

## Vanilla Usage

If you want to use the iFrame solution without the DNA Design Element you can use the following code:
```html
<!-- Works for youtube and vimeo, just add the name of the service and the id -->
<div data-service="youtube|vimeo" data-id="add-your-id" data-autoscale></div>
```

## Only IFrameManager (without Cookie Consent Modal)

If you want to use the IFrame without the Cookie Consent you can disable the modal like this in your yml:
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

## Dynamic iFrame Loading

When iframes are injected into the DOM dynamically (e.g. opening a dialog with embedded content), iframemanager needs to be reset and reinitialized so it picks up the new elements.

This module exposes `window.initIFrameManager` for exactly this purpose. It is set automatically when `handleCookieConsentDialog()` runs and holds a reference to the internal init function with all config already bound via closure.

**Usage:**
```js
// Reset iframemanager (clears all tracked iframes page-wide)
// then reinitialize so it scans the DOM including the new content
iframemanager().reset();
window.initIFrameManager();
```

**Full example — dynamically appended dialog:**
```js
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.querySelector('#open-dynamic-dialog');

    btn.addEventListener('click', () => {
        // Remove any previously appended dialog to avoid duplicate IDs
        document.querySelector('#embedded-dialog')?.remove();

        const tmp = document.createElement('div');
        tmp.innerHTML = "<dialog id='embedded-dialog'>" + embeddedContent + "</dialog>";
        const newDialog = tmp.firstElementChild;

        // Clean up when the dialog is closed
        newDialog.addEventListener('close', () => newDialog.remove());

        document.body.append(newDialog);

        // Guard in case handleCookieConsentDialog hasn't run yet
        if (typeof window.initIFrameManager === 'function') {
            iframemanager().reset();
            window.initIFrameManager();
        }

        newDialog.showModal();
    });
});
```

**Important:** `iframemanager().reset()` is page-wide — it resets all tracked iframes on the page, not just the newly added ones. If your page has other already-accepted and loaded iframes outside the dialog, they will be hidden until the user re-accepts them. Keep this in mind if you use dynamic loading alongside static embedded content.

For further information have a look at the [Cookie Consent Documentation - iFrameManager](https://cookieconsent.orestbida.com/advanced/iframemanager-setup.html)