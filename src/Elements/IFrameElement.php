<?php

namespace Pixelpoems\VanillaCookieConsent\Elements;

use Override;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use VanillaCookieConsent\Services\CCService;

if (!class_exists(BaseElement::class)) {
    return;
}

class IFrameElement extends BaseElement
{
    private static $singular_name = 'IFrame Element';

    private static $plural_name = 'IFrame Elements';

    private static $class_description = 'Shows embedded iframes or self hosted videos';

    private static $table_name = 'Pixelpoems_IFrameElement';

    private static $icon = 'font-icon-block-media';

    private static $controller_template = 'IFrameElement';

    private static $inline_editable = false;

    private static $db = [
        'EmbeddedID' => 'Varchar',
        'SourceType' => 'Varchar',
        'iFrameTitle' => 'Varchar',
    ];

    private static $has_one = [
        'Video' => File::class,
    ];

    private static $owns = [
        'Video',
    ];

    #[Override]
    public function getType(): string
    {
        return 'IFrame Element';
    }

    #[Override]
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'EmbeddedID',
            'Video',
            'SourceType',
            'iFrameTitle'
        ]);

        $allowedEmbeds = CCService::config()->get('iframe_services');
        $allowedEmbedsList = [];
        foreach ($allowedEmbeds as $allowedEmbed) {
            $allowedEmbedsList[$allowedEmbed] = match ($allowedEmbed) {
                'googlemaps' => 'Google Maps',
                'youtube' => 'YouTube',
                'vimeo' => 'Vimeo',
                'yumpu' => 'Yumpu',
                default => ucfirst((string) $allowedEmbed),
            };
        }
        $allowedEmbedsList['self-host'] = 'Self Hosted';

        $fields->addFieldsToTab('Root.Main', [
            OptionsetField::create('SourceType', 'SourceType of Video', $allowedEmbedsList)
                ->setDescription('Please hit "Saved" or "Publish", for further settings')
        ]);

        if ($this->isEmbedded()) {

            $embeddedInfo = '';
            switch ($this->SourceType) {
                case 'googlemaps':
                    $embeddedInfo = 'The Google Maps ID is the part after the "https://www.google.com/maps/embed?pb=" in the URL';
                    break;
                case 'youtube':
                    $embeddedInfo = 'The YouTube ID is the part after the "https://www.youtube.com/embed/" in the URL';
                    break;
                case 'vimeo':
                    $embeddedInfo = 'The Vimeo ID is the part after the "https://player.vimeo.com/video/" in the URL';
                    break;
                case 'yumpu':
                    $embeddedInfo = 'The Yumpu ID is the part after the "https://www.yumpu.com/en/embed/view/" in the URL';
                    break;
            }

            $fields->addFieldsToTab('Root.Main', [
                LiteralField::create('EmbeddedIDInfo', '<p class="message">' . $embeddedInfo . '</p>'),
                TextField::create('EmbeddedID', 'Embedded ID')
                    ->setDescription( 'Code for embedded (e.g. YouTube ID, Vimeo ID, Yumpu ID)'),
                TextField::create('iFrameTitle', 'iFrame Title')
                    ->setDescription('Title for iFrame (optional - for accessibility)')
            ]);
        }

        if ($this->isUpload()) {
            $fields->addFieldToTab('Root.Main',
                UploadField::create('Video', 'Video')
                    ->setFolderName('Videos')
                    ->setAllowedExtensions(['mp4'])
                    ->setDescription('allowed file type: .mp4')
            );
        }

        $fields->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function isEmbedded(): bool
    {
        return $this->SourceType === 'youtube' || $this->SourceType === 'vimeo' || $this->SourceType === 'yumpu'
            || $this->SourceType === 'googlemaps';
    }

    public function isUpload(): bool
    {
        return $this->SourceType === 'self-host';
    }


    #[Override]
    public function getBlockSchema(): array
    {
        $blockSchema = parent::getBlockSchema();

        $blockSchema['content'] = $this->SourceType;
        return $blockSchema;
    }
}
