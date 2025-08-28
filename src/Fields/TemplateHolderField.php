<?php
namespace VanillaCookieConsent\Fields;

use SilverStripe\Forms\DatalessField;

class TemplateHolderField extends DatalessField
{
    protected $schemaDataType = TemplateHolderField::SCHEMA_DATA_TYPE_STRUCTURAL;

    protected $schemaComponent = 'TemplateHolderField';

    protected $template = 'VanillaCookieConsent\TemplateHolderField';


    public function __construct($name, $title = null, $template = null)
    {
        $this->setTemplate($template);
        parent::__construct($name, $title);
    }


    public function setTemplate($template)
    {
        if ($template) {
            $this->template = $template;
        }
        return $this;
    }
}
