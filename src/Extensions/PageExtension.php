<?php

namespace VanillaCookieConsent\Extensions;

use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;
use VanillaCookieConsent\Services\CCService;

class PageExtension extends DataExtension
{
    private static array $db = [
        'HideCookieConsent' => 'Boolean'
    ];

    private static array $defaults = [
        'HideCookieConsent' => false
    ];

    public function updateSettingsFields($fields)
    {
        $fields->addFieldsToTab('Root.Settings', [
            Tab::create('CookieConsent', 'Cookie Consent')
        ], 'Visibility');

        $fields->addFieldsToTab('Root.Settings.CookieConsent', [
            CheckboxField::create('HideCookieConsent', 'Hide Cookie Consent on this Page?')
        ]);
    }

    public function getCCJSConfig()
    {
        $service = CCService::create();
        return $service->CCJSConfig();
    }
}
