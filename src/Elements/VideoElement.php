<?php

namespace Pixelpoems\VanillaCookieConsent\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

class VideoElement extends BaseElement
{
    private static $singular_name = 'VideoElement';

    private static $plural_name = 'VideoElements';

    private static $description = 'Shows embedded or self hosted videos';

    private static $table_name = 'VideoElement';

    private static $icon = 'font-icon-block-media';

    private static $controller_template = 'VideoElement';

    private static $inline_editable = false;

    private static $db = [
        'EmbeddedVideoID' => 'Varchar',
        'SourceType' => 'Varchar',
    ];

    private static $has_one = [
        'Video' => File::class,
    ];

    private static $owns = [
        'Video',
    ];

    public function getType(): string
    {
        return 'Video Element';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'EmbeddedVideoID',
            'Video',
            'SourceType'
        ]);

        $fields->addFieldsToTab('Root.Main', [
            OptionsetField::create('SourceType', 'SourceType of Video', [
                'youtube' => 'Youtube',
                'vimeo' => 'Vimeo',
                'self-host' => 'Self Hosted'
            ])->setDescription('Please hit "Saved" or "Publish", for further settings')
        ]);

        if ($this->isEmbedded()) {
            $fields->addFieldToTab('Root.Main',
                TextField::create('EmbeddedVideoID', 'Embedded Video ID')
                    ->setDescription( 'Code for embedded video')
            );
        }

        if ($this->isUpload()) {
            $fields->addFieldToTab('Root.Main',
                UploadField::create('Video', 'Video')
                    ->setFolderName('Videos')
                    ->setAllowedExtensions(['mp4'])
                    ->setDescription('allowed file type: .mp4')
            );
        }

        return $fields;
    }

    public function isEmbedded(): bool
    {
        return $this->SourceType === 'youtube' || $this->SourceType === 'vimeo';
    }

    public function isUpload(): bool
    {
        return $this->SourceType === 'isUpload';
    }
}
