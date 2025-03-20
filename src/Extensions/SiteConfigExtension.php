<?php

namespace VanillaCookieConsent\Extensions;


use Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;
use TractorCow\Fluent\Extension\FluentExtension;

class SiteConfigExtension extends DataExtension
{
    private static array $db = [
        'CCActive' => 'Boolean',
        'DisplayOnLogin' => 'Boolean',
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
                CheckboxField::create('CCActive', 'Cookie Consent Active')
                    ->setDescription('Enable or disable the cookie consent modal'),
                CheckboxField::create('DisplayOnLogin', 'Display on Login Page')
                    ->setDescription('Display the cookie consent modal on the login page')
            )->setTitle('General Settings'),
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

        if($this->owner->hasExtension(FluentExtension::class)) {
            $this->owner->updateFluentLocalisedFields($fields);
        }

        parent::updateCMSFields($fields);
    }
}
