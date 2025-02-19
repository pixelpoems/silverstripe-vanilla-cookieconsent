<?php

namespace VanillaCookieConsent\Extensions;

use Pixelpoems\Search\Services\SearchConfig;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Requirements;

class PageControllerExtension extends Extension
{

    public function onBeforeInit()
    {
        if ($this->owner->data()->getDisplayCookieConsent()) {
            Requirements::javascript('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/javascript/vanilla-cookie-consent.min.js');
            Requirements::css('pixelpoems/silverstripe-vanilla-cookieconsent:client/dist/css/vanilla-cookie-consent.min.css');
        }
    }
}
