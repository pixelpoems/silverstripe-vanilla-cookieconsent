<?php

namespace VanillaCookieConsent\Services;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ViewableData;
use SilverStripe\View\ViewableData_Customised;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

class CCService extends Controller
{
    use Injectable;
    use Configurable;

    public function CCJSConfig(): string
    {
        $config = [
            'categories' => [],
            'language' => [
                'default' => self::config()->get('default_lang'),
                'translations' => []
            ]
        ];

        if(self::config()->get('categories')) {
            foreach (self::config()->get('categories') as $category) {
                $config['categories'][$category] = [];
            }
        }

        if (ModuleLoader::inst()->getManifest()->moduleExists('tractorcow/silverstripe-fluent') && self::config()->get('languages')) {

            $currentLocale = Locale::getCurrentLocale();
            $langCode = substr($currentLocale->Locale, 0, 2);
            $config['language']['default'] = $langCode;
            i18n::set_locale($langCode);


            $config['language']['translations'][$langCode] = FluentState::singleton()->withState(function ($state) use ($currentLocale) {
                $state->setLocale($currentLocale->Locale);
                return $this->getLanguageData();
            });

        } else {
            // No fluent we use the default language
            $config['language']['translations'][self::config()->get('default_lang')] = [
                ...$this->getLanguageData()
            ];
        }

        $this->extend('updateCCJSConfig', $config);

        return json_encode($config, JSON_OBJECT_AS_ARRAY);
    }

    private function getLanguageData(): array
    {

        // HTML SiteTree Links are not working in the modal description so we need to convert them to absolute links!!
        if(SiteConfig::current_site_config()->ModalDescription) {
            $modalDescription = DBHTMLText::create_field('HTMLText',  SiteConfig::current_site_config()->ModalDescription);
            $modalDescription = $modalDescription?->AbsoluteLinks();
        } else {
            $modalDescription = _t('VanillaCookieConsent\ConsentModal.Description', 'We use cookies to provide you with the best experience on our website.');
        }

        $data = [
            'consentModal' => [
                'title' => SiteConfig::current_site_config()->ModalTitle ?: _t('VanillaCookieConsent\ConsentModal.Title', 'We use cookies'),
                'description' => $modalDescription,
                'acceptAllBtn' => _t('VanillaCookieConsent\Buttons.AcceptAll', 'Accept all'),
                'acceptNecessaryBtn' => _t('VanillaCookieConsent\Buttons.AcceptNecessary', 'Accept necessary'),
                'showPreferencesBtn' => _t('VanillaCookieConsent\Buttons.ShowPreferences', 'Show preferences'),
            ],
            'preferencesModal' => [
                'title' => _t('VanillaCookieConsent\PreferencesModal.Title', 'Manage Cookie preferences'),
                'acceptAllBtn' => _t('VanillaCookieConsent\Buttons.AcceptAll', 'Accept all'),
                'acceptNecessaryBtn' => _t('VanillaCookieConsent\Buttons.AcceptNecessary', 'Accept necessary'),
                'savePreferencesBtn' => _t('VanillaCookieConsent\Buttons.SavePreferences', 'Save preferences'),
                'closeIconLabel' => _t('VanillaCookieConsent\PreferencesModal.CloseIconLabel', 'Close'),
                'sections' => []
            ],
        ];

        $categorySections = [];

        // HTML SiteTree Links are not working in the modal description so we need to convert them to absolute links!!
        if(SiteConfig::current_site_config()->TextBlockTitle ||SiteConfig::current_site_config()->TextBlockDescription) {
            $descripiton = DBHTMLText::create_field('HTMLText',  SiteConfig::current_site_config()->TextBlockDescription);

            $categorySections[] = [
                'title' => SiteConfig::current_site_config()->TextBlockTitle,
                'description' => $descripiton?->AbsoluteLinks()
            ];
        }

        $categorySections[] = [
            'title' => _t('VanillaCookieConsent\Categories.Necessary', 'Necessary'),
            'description' => _t('VanillaCookieConsent\Categories.NecessaryDescription', 'These cookies are necessary for the website to function.'),
            'linkedCategory' => 'necessary'
        ];

        if(self::config()->get('categories')) {
            foreach (self::config()->get('categories') as $category) {

            $categoryKey = ucfirst(str_replace(' ', '', $category));

            $categoryData = [
                'title' => _t('VanillaCookieConsent\Categories.' . $categoryKey, $category),
                'description' => _t('VanillaCookieConsent\Categories.' . $categoryKey . 'Description', '__ CATEGORY DESCRIPTION'),
                'linkedCategory' => $category
            ];

            if($cookies = self::config()->get($category . '_cookie_table')) {

                $cookieTableData = [];
                foreach ($cookies as $cookie => $domain) {
                    $cookieTableData[] = [
                        'name' => $cookie,
                        'domain' => $domain,
                        'desc' => _t('VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie, '__ COOKIE DESCRIPTION (' . ucfirst($category) . '_Cookie' . $cookie . ')'),
                        'storage' => _t('VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie . '_Storage', '__ COOKIE STORAGE (' . ucfirst($category) . '_Cookie' . $cookie . ')')
                    ];
                }

                $categoryData['cookieTable'] = [
//                    'caption' => 'Cookie Table',
                    'headers' => [
                        'name' => _t('VanillaCookieConsent\CookieTable.Cookie', 'Cookie'),
                        'domain' => _t('VanillaCookieConsent\CookieTable.Domain', 'Domain'),
                        'desc' => _t('VanillaCookieConsent\CookieTable.Description', 'Description'),
                        'storage' => _t('VanillaCookieConsent\CookieTable.Storage', 'Storage'),
                    ],
                    'body' => $cookieTableData
                ];
            }

            $categorySections[] = $categoryData;
        }
        }

        $data['preferencesModal']['sections'] = array_values($categorySections);
        return $data;
    }

}
