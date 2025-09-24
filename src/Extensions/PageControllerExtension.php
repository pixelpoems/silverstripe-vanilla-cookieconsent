<?php

namespace VanillaCookieConsent\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;
use VanillaCookieConsent\Services\CCService;

class PageControllerExtension extends Extension
{

    public function onBeforeInit()
    {
        if (SiteConfig::current_site_config()->shouldIncludeConfig()) {
            if(!CCService::config()->get('disable_default_css')) {
                Requirements::css('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/css/vanilla-cookie-consent.min.css');
            }

            if(!CCService::config()->get('disable_default_js')) {
                Requirements::javascript('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/javascript/vanilla-cookie-consent.min.js', [
                    'defer' => true,
                ]);
            }
        }
    }
}
