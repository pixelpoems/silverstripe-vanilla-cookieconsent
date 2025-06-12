<?php

namespace VanillaCookieConsent\Services;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\SiteConfig\SiteConfig;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

class CCService extends Controller
{
    use Injectable;
    use Configurable;

    private static array $iframe_services = [];

    private static array $default_iframe_services = [
        'googlemaps',
        'youtube',
        'vimeo',
        'yumpu',
    ];

    private function getIFrameServices()
    {
        if (self::config()->get('iframe_services')) {
            return self::config()->get('iframe_services');
        } else {
            return self::$default_iframe_services;
        }
    }

    public function CCJSConfig(): string
    {
        $config = [
            'categories' => [],
            'enableIFrameManager' => self::config()->get('enable_iframe_manager'),
            'enableConsentModal' => self::config()->get('enable_consent_modal'),
            'language' => [
                'default' => self::config()->get('default_lang'),
                'translations' => []
            ]
        ];

        if($categories = self::config()->get('categories')) {
            if (array_is_list($categories)) {
                // Convert list-style array into an associative array with empty arrays as values
                $categories = array_fill_keys($categories, []);
            }

            foreach ($categories as $category => $services) {
                if($services) $config['categories'][$category]['services'] = $services;
                else $config['categories'][$category] = [];

                if($services) {
                    foreach ($services as $service) {
                        $config['services'][] = $service;
                    }
                } else $config['services'] = null;
            }
        }

        // Translations needed for iframemanager and cookie consent
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
        $data = [];

        if(self::config()->get('enable_iframe_manager')) {
            $data['iframeManager'] = [
                'loadBtn' => _t('VanillaCookieConsent\IframeManager.LoadBtn', 'Load Once'),
                'loadAllBtn' => _t('VanillaCookieConsent\IframeManager.LoadAllBtn', 'Don\'t ask again'),
                'notices' => [] // Content will be loaded l173-177
            ];
        }

        if(self::config()->get('enable_consent_modal')) {

            // HTML SiteTree Links are not working in the modal description so we need to convert them to absolute links!!
            if(SiteConfig::current_site_config()->ModalDescription) {
                $modalDescription = DBHTMLText::create_field('HTMLText',  SiteConfig::current_site_config()->ModalDescription);
                $modalDescription = $modalDescription?->AbsoluteLinks();
            } else {
                $modalDescription = _t('VanillaCookieConsent\ConsentModal.Description', 'We use cookies to provide you with the best experience on our website.');
            }

            $data['consentModal'] = [
                'title' => SiteConfig::current_site_config()->ModalTitle ?: _t('VanillaCookieConsent\ConsentModal.Title', 'We use cookies'),
                'description' => $modalDescription,
                'acceptAllBtn' => _t('VanillaCookieConsent\Buttons.AcceptAll', 'Accept all'),
                'acceptNecessaryBtn' => _t('VanillaCookieConsent\Buttons.AcceptNecessary', 'Accept necessary'),
                'showPreferencesBtn' => _t('VanillaCookieConsent\Buttons.ShowPreferences', 'Show preferences'),
            ];

            $data['preferencesModal'] =  [
                'title' => _t('VanillaCookieConsent\PreferencesModal.Title', 'Manage Cookie preferences'),
                'acceptAllBtn' => _t('VanillaCookieConsent\Buttons.AcceptAll', 'Accept all'),
                'acceptNecessaryBtn' => _t('VanillaCookieConsent\Buttons.AcceptNecessary', 'Accept necessary'),
                'savePreferencesBtn' => _t('VanillaCookieConsent\Buttons.SavePreferences', 'Save preferences'),
                'closeIconLabel' => _t('VanillaCookieConsent\PreferencesModal.CloseIconLabel', 'Close'),
                'sections' => []
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

            if($categories = self::config()->get('categories')) {

                if (array_is_list($categories)) {
                    // Convert list-style array into an associative array with empty arrays as values
                    $categories = array_fill_keys($categories, []);
                }

                foreach ($categories as $category => $services) {

                    $categoryKey = ucfirst(str_replace(' ', '', $category));

                    $categoryData = [
                        'title' => _t('VanillaCookieConsent\Categories.' . $categoryKey, 'VanillaCookieConsent\Categories.' . $categoryKey),
                        'description' => _t('VanillaCookieConsent\Categories.' . $categoryKey . 'Description', 'VanillaCookieConsent\Categories.' . $categoryKey . 'Description'),
                        'linkedCategory' => $category
                    ];

                    if($cookies = self::config()->get($category . '_cookie_table')) {

                        $cookieTableData = [];
                        foreach ($cookies as $cookie => $domain) {
                            $cookieTableData[] = [
                                'name' => $cookie,
                                'domain' => $domain,
                                'desc' => _t('VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie, 'VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie),
                                'storage' => _t('VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie . '_Storage', 'VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie_' . $cookie . '_Storage')
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
        }

        foreach ($this->getIFrameServices() as $service) {
            $data['iframeManager']['notices'][$service] = _t('VanillaCookieConsent\IframeManager.Notice_' . $service, 'This content is hosted by a third party. By showing the external content you accept the terms and conditions of ' . strtolower((string) $service) . '.');
        }

        return $data;
    }
}
