<?php

namespace VanillaCookieConsent\Extensions;


use Page;
use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Security;
use TractorCow\Fluent\Extension\FluentExtension;
use VanillaCookieConsent\Services\CCService;

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

        $ymlconfig_modalinfo = '<p class="message good">Cookie Consent Modal is availible (YML ACTIVE)</p>';
        $isModalEnabled = CCService::config()->get('enable_consent_modal');
        if(!$isModalEnabled) $ymlconfig_modalinfo = '<p class="message bad">Cookie Consent Modal is not availibe (YML INACTIVE)</p>';

        $ymlconfig_iframeinfo = '<p class="message good">IFrame Manager is availible (YML ACTIVE)</p>';
        $isIFrameEnabled = CCService::config()->get('enable_iframe_manager');
        if(!$isIFrameEnabled) $ymlconfig_iframeinfo = '<p class="message bad">IFrame Manager is not availibe (YML INACTIVE)</p>';

        $fields->addFieldsToTab('Root.CookieConsent', [
            LiteralField::create('YMLModalConfigInfo', $ymlconfig_modalinfo),
            LiteralField::create('YMLIFrameConfigInfo', $ymlconfig_iframeinfo),
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
           )->setTitle('Preferences Text Block')
            ->setDescription('This text block will be displayed in the preferences modal above the categories')
        ]);

        if($this->owner->hasExtension(FluentExtension::class)) {
            $this->owner->updateFluentLocalisedFields($fields);
        }

        parent::updateCMSFields($fields);
    }

    public function getDisplayCookieConsent()
    {
        if(!$this->owner->CCActive) return false;

        $controller = Controller::curr();
        if($controller instanceof PageController) {
            $currentPage = $controller->data();
            if($currentPage->HideCookieConsent) return false;
            if($currentPage->ClassName === ErrorPage::class) return false;
        }

        if($controller instanceof Security && !$this->owner->DisplayOnLogin) {
            return false;
        }

        return true;

    }
}
