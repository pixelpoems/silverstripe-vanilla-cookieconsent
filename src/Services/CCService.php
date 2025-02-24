<?php

namespace VanillaCookieConsent\Services;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Manifest\ModuleLoader;
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

        foreach (self::config()->get('categories') as $category) {
            $config['categories'][$category] = [];
        }

        if (ModuleLoader::inst()->getManifest()->moduleExists('tractorcow/silverstripe-fluent') && self::config()->get('languages')) {

            foreach (self::config()->get('languages') as $language) {
                $locale = Locale::get()->filter('Locale:StartsWith', $language)->first();
                if(!$locale) continue;

                $config['language']['translations'][$language] = FluentState::singleton()->withState(function ($state) use ($locale) {
                    $state->setLocale($locale->Locale);
                    return $this->getLanguageData();
                });
            }

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
        $data = [
            'consentModal' => [
                'title' => _t('VanillaCookieConsent\ConsentModal.Title', 'We use cookies'),
                'description' => _t('VanillaCookieConsent\ConsentModal.Description', 'We use cookies to provide you with the best experience on our website.'),
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
                        'desc' => _t('VanillaCookieConsent\Categories.' . $categoryKey . '_Cookie' . $cookie, '__ COOKIE DESCRIPTION (' . ucfirst($category) . '_Cookie' . $cookie . ')')
                    ];
                }

                $categoryData['cookieTable'] = [
                    'caption' => 'Cookie Table', // ToDo: Translate
                    'headers' => [
                        'name' => 'Cookie', // ToDo: Translate
                        'domain' => 'Domain', // ToDo: Translate
                        'desc' => 'Description', // ToDo: Translate
                    ],
                    'body' => $cookieTableData
                ];
            }

            $categorySections[] = $categoryData;
        }

        $data['preferencesModal']['sections'] = array_values($categorySections);
        return $data;
    }

}
