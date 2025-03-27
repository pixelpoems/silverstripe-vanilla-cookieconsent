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

    private static array $default_allowed_video_embeds = [
        'youtube',
        'vimeo',
        'yumpu',
    ];

    private static $db = [
        'EmbeddedVideoID' => 'Varchar',
        'SourceType' => 'Varchar',
        'iFrameTitle' => 'Varchar',
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
            'SourceType',
            'iFrameTitle'
        ]);

        $allowedVideoEmbeds = self::config()->get('allowed_video_embeds');
        if(!count($allowedVideoEmbeds)) $allowedVideoEmbeds = self::$default_allowed_video_embeds;
        $allowedVideoEmbedsList = [];
        foreach ($allowedVideoEmbeds as $allowedVideoEmbed) {
            $allowedVideoEmbedsList[$allowedVideoEmbed] = ucfirst($allowedVideoEmbed);
        }
        $allowedVideoEmbedsList['self-host'] = 'Self Hosted';

        $fields->addFieldsToTab('Root.Main', [
            OptionsetField::create('SourceType', 'SourceType of Video', $allowedVideoEmbedsList)
                ->setDescription('Please hit "Saved" or "Publish", for further settings')
        ]);

        if ($this->isEmbedded()) {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('EmbeddedVideoID', 'Embedded Video ID')
                    ->setDescription( 'Code for embedded video'),
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

        return $fields;
    }

    public function isEmbedded(): bool
    {
        return $this->SourceType === 'youtube' || $this->SourceType === 'vimeo' || $this->SourceType === 'yumpu';
    }

    public function isUpload(): bool
    {
        return $this->SourceType === 'isUpload';
    }


    public function getBlockSchema()
    {
        $blockSchema = parent::getBlockSchema();

        $blockSchema['content'] = $this->SourceType;
        return $blockSchema;
    }
}
