<?php

namespace VanillaCookieConsent\Extensions;


use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Parsers\ShortcodeParser;
use VanillaCookieConsent\Services\CCService;


class IFrameTinyExtension extends Extension
{

    public function Embeded()
    {
        // Suppress XML encoding for DBHtmlText
        $forTemplate = $this->owner;

        if (!CCService::config()->get('enable_iframe_manager')) {
            return $forTemplate->RAW();
        }

        $forTemplate = preg_replace_callback('/<iframe[^>]+src="([^"]+)"[^>]*><\/iframe>/i', function ($matches) {
            $src = $matches[1];
            $service = '';
            $id = '';
            $title = '';

            // Detect provider and extract ID
            if (preg_match('#youtube\.com/embed/([^\?&"/]+)#', $src, $m)) {
                $service = 'youtube';
                $id = $m[1];
            } elseif (preg_match('#player\.vimeo\.com/video/([^\?&"/]+)#', $src, $m)) {
                $service = 'vimeo';
                $id = $m[1];
            } elseif (preg_match('#yumpu\.com/en/embed/view/([^\?&"/]+)#', $src, $m)) {
                $service = 'yump';
                $id = $m[1];
            }

            // Try to grab title if available
            if (preg_match('/title="([^"]+)"/i', $matches[0], $m)) {
                $title = htmlspecialchars($m[1], ENT_QUOTES);
                return sprintf('<div data-service="%s" data-id="%s" data-title="%s" data-autoscale></div>', $service, $id, $title);
            } else {
                return sprintf('<div data-service="%s" data-id="%s" data-autoscale></div>', $service, $id);
            }
        }, $forTemplate);


        $forTemplate = DBHTMLText::create_field('HTMLText', $forTemplate);
        return $forTemplate;
    }

}
