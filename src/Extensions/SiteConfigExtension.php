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
        'ModalTitle' => 'Varchar(255)',
        'ModalDescription' => 'HTMLText',
        'TextBlockTitle' => 'Varchar(255)',
        'TextBlockDescription' => 'HTMLText'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->findOrMakeTab('Root.CookieConsent', 'Cookie Consent');

        $fields->addFieldsToTab('Root.CookieConsent', [
            CompositeField::create(
                TextField::create('ModalTitle', 'Modal Title')
                    ->setDescription('Fallback: ' . _t('VanillaCookieConsent\ConsentModal.Title', 'We use cookies')),
                HTMLEditorField::create('ModalDescription', 'Modal Description')
                        ->setDescription('Fallback: ' . _t('VanillaCookieConsent\ConsentModal.Description', 'We use cookies to give you the best possible experience.'))
                    ->setRows(5)
            )->setTitle('Modal'),
            CompositeField::create(
               TextField::create('TextBlockTitle', 'Text Block Title'),
               HTMLEditorField::create('TextBlockDescription', 'Text Block Description')
                ->setRows(5)
           )->setTitle('Text Block')
            ->setDescription('This text block will be displayed in the preferences modal above the categories')
        ]);

        parent::updateCMSFields($fields);
    }
}
