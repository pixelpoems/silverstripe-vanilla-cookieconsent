<?php

namespace VanillaCookieConsent\Extensions;


use Page;
use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use TractorCow\Fluent\Extension\FluentExtension;
use VanillaCookieConsent\Fields\TemplateHolderField;
use VanillaCookieConsent\Models\Insight;
use VanillaCookieConsent\Services\CCService;

class SiteConfigExtension extends Extension
{
    private static array $db = [
        'CCActive' => 'Boolean',
        'DisplayOnLogin' => 'Boolean',
        'SavePeriodForInsights' => 'Int', // in days
        'ModalTitle' => 'Varchar(255)',
        'ModalDescription' => 'HTMLText',
        'TextBlockTitle' => 'Varchar(255)',
        'TextBlockDescription' => 'HTMLText'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->findOrMakeTab('Root.CookieConsent', 'Cookie Consent');

        $ymlconfig_modalinfo = '<p class="message good">Cookie Consent Modal is available (YML ACTIVE) - You need to set it active below.</p>';
        $isModalEnabled = CCService::config()->get('enable_consent_modal');
        if(!$isModalEnabled) $ymlconfig_modalinfo = '<p class="message bad">Cookie Consent Modal is not available (YML INACTIVE)</p>';

        $ymlconfig_iframeinfo = '<p class="message good">IFrame Manager is active (YML ACTIVE)</p>';
        $isIFrameEnabled = CCService::config()->get('enable_iframe_manager');
        if(!$isIFrameEnabled) $ymlconfig_iframeinfo = '<p class="message bad">IFrame Manager is not active (YML INACTIVE)</p>';


        $fields->addFieldsToTab('Root.CookieConsent', [
            LiteralField::create('YMLModalConfigInfo', $ymlconfig_modalinfo),
            LiteralField::create('YMLIFrameConfigInfo', $ymlconfig_iframeinfo)
        ]);

        if($isModalEnabled) {
            $fields->addFieldsToTab('Root.CookieConsent', [
                CompositeField::create(
                    CheckboxField::create('CCActive', 'Cookie Consent Active')
                        ->setDescription('Enable or disable the cookie consent modal'),
                    CheckboxField::create('DisplayOnLogin', 'Display on Login Page')
                        ->setDescription('Display the cookie consent modal on the login page'),
                    NumericField::create('SavePeriodForInsights', 'Save Period for Insights (in days)')
                        ->setDescription('The period in days for which insights will be saved. Set to 0 to disable insights.')
                )->setTitle('General Settings')->setName('GeneralSettings'),
                ToggleCompositeField::create('Modal', 'Modal Settings', [
                    TextField::create('ModalTitle', 'Modal Title')
                        ->setDescription('Fallback: ' . _t('VanillaCookieConsent\ConsentModal.Title', 'We use cookies')),
                    HTMLEditorField::create('ModalDescription', 'Modal Description')
                        ->setDescription('Fallback: ' . _t('VanillaCookieConsent\ConsentModal.Description', 'We use cookies to give you the best possible experience.'))
                        ->setRows(5)
                ]),
                ToggleCompositeField::create('PreferencesTextBlockSettings', 'Preferences Text Block', [
                    TextField::create('TextBlockTitle', 'Text Block Title'),
                    HTMLEditorField::create('TextBlockDescription', 'Text Block Description')
                        ->setRows(5)
                ])->setDescription('This text block will be displayed in the preferences modal above the categories'),
            ]);

            if($this->owner->SavePeriodForInsights) {
                $fields->addFieldsToTab('Root.CookieConsent', [
                    TemplateHolderField::create('VisiualInsights', 'Insights', 'VanillaCookieConsent/ConsentInsights'),
                    GridField::create(
                        'Insights',
                        'Insights',
                        Insight::get(),
                        GridFieldConfig_RecordViewer::create()
                    )
                ]);
            }
        }


        if($this->owner->hasExtension(FluentExtension::class)) {
            $this->owner->updateFluentLocalisedFields($fields);
        }
    }

    public function shouldIncludeConfig()
    {
        // Check if the iframe manager is enabled
        if(CCService::config()->get('enable_iframe_manager')) {
            return true;
        }

        // Check if the cookie consent modal is enabled
        return $this->shouldIncludeDialog();
    }

    public function shouldIncludeDialog()
    {
        // Check if the cookie consent modal is active
        if(CCService::config()->get('enable_consent_modal')) {
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

        return false;
    }

    public function getCCInsightData()
    {
        $insights = Insight::get();
        $categories = CCService::config()->get('categories');

        $categoriesForTemplate = [];

        // Add Nessessary Category - this is the default category that is always accepted
        $categoriesForTemplate[] = [
            'Title' => 'Necessary',
            'Accepts' => $insights->count(),
            'Rejects' => 0,
        ];

        foreach($categories as $category) {

            $accepts = $insights->filter(['ConsentType' => 'Accepted'])->count();
            $partlyAccept = $insights->filter(['ConsentType' => 'Partly', 'AcceptedCategories:PartialMatch' => $category])->count();
            $rejects = $insights->filter(['ConsentType' => 'Rejected'])->count();
            $partlyReject = $insights->count() - $accepts - $partlyAccept - $rejects;

            $catData = [
                'Title' => ucfirst($category),
                'Accepts' => $accepts + $partlyAccept,
                'Rejects' => $rejects + $partlyReject,
            ];

            $categoriesForTemplate[] = $catData;
        }

        return ArrayData::create([
            'SavePeriodForInsights' => $this->owner->SavePeriodForInsights,
            'Consents' => ArrayData::create([
                'Total' => $insights->count(),
                'Accepted' => $insights->filter(['ConsentType' => 'Accepted'])->count(),
                'Rejected' => $insights->filter(['ConsentType' => 'Rejected'])->count(),
                'Partly' => $insights->filter(['ConsentType' => 'Partly'])->count(),
            ]),
            'Categories' => json_encode($categoriesForTemplate)

        ]);

    }
}
