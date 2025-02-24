<?php

namespace VanillaCookieConsent\Extensions;


use Page;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;

class SiteConfigExtension extends DataExtension
{
    private static array $db = [
        'TextBlockTitle' => 'Varchar(255)',
        'TextBlockDescription' => 'HTMLText'
    ];

    private static array $has_one = [
        'DataProtectionPage' => Page::class,
        'ImprintPage' => Page::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->findOrMakeTab('Root.CookieConsent', 'Cookie Consent');

        $fields->addFieldsToTab('Root.CookieConsent', [
            CompositeField::create(
                TreeDropdownField::create('DataProtectionPageID', 'Datenschutzseite', Page::class),
                TreeDropdownField::create('ImprintPageID', 'Impressumseite', Page::class)
            )
                ->setTitle('Cookie Consent Pages')
                ->setDescription('Select the pages that contain your data protection and imprint information. They will be displayed on the custom Modal only'),
           CompositeField::create(
               TextField::create('TextBlockTitle', 'Text Block Title'),
               HTMLEditorField::create('TextBlockDescription', 'Text Block Description')
                ->setRows(5)
               ->set
           )->setTitle('Text Block')
            ->setDescription('This text block will be displayed in the preferences modal above the categories')
        ]);

        parent::updateCMSFields($fields);
    }
}
