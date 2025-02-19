<?php

namespace VanillaCookieConsent\Extensions;


use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;

class SiteConfigExtension extends DataExtension
{

    private static array $has_one = [
        'DataProtectionPage' => Page::class,
        'ImprintPage' => Page::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->findOrMakeTab('Root.CookieConsent', 'Cookie Consent');

        $fields->addFieldsToTab('Root.CookieConsent', [
            TreeDropdownField::create('DataProtectionPageID', 'Datenschutzseite', Page::class),
            TreeDropdownField::create('ImprintPageID', 'Impressumseite', Page::class)
        ]);

        parent::updateCMSFields($fields);
    }
}
