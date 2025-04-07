<?php

namespace Pixelpoems\VanillaCookieConsent\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

class IFrameElement extends BaseElement
{
    private static $singular_name = 'IFrame Element';

    private static $plural_name = 'IFrame Elements';

    private static $description = 'Shows embedded iframes or self hosted videos';

    private static $table_name = 'Pixelpoems_IFrameElement';

    private static $icon = 'font-icon-block-media';

    private static $controller_template = 'IFrameElement';

    private static $inline_editable = false;

    private static array $default_allowed_embeds = [
        'youtube',
        'vimeo',
        'yumpu',
    ];

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

    public function getType(): string
    {
        return 'IFrame Element';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'EmbeddedID',
            'Video',
            'SourceType',
            'iFrameTitle'
        ]);

        $allowedEmbeds = self::config()->get('allowed_embeds');
        if(!$allowedEmbeds) $allowedEmbeds = self::$default_allowed_embeds;
        $allowedEmbedsList = [];
        foreach ($allowedEmbeds as $allowedEmbed) {
            $allowedEmbedsList[$allowedEmbed] = ucfirst($allowedEmbed);
        }
        $allowedEmbedsList['self-host'] = 'Self Hosted';

        $fields->addFieldsToTab('Root.Main', [
            OptionsetField::create('SourceType', 'SourceType of Video', $allowedEmbedsList)
                ->setDescription('Please hit "Saved" or "Publish", for further settings')
        ]);

        if ($this->isEmbedded()) {
            $fields->addFieldsToTab('Root.Main', [
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
