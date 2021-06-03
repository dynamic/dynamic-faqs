<?php

namespace Dynamic\FAQ\Page;

use Dynamic\FAQ\Model\FAQ;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class FAQPage extends \Page
{
    /**
     * @var string
     */
    private static $singular_name = 'FAQ Page';

    /**
     * @var string
     */
    private static $plural_name = 'FAQ Pages';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            if ($this->ID) {
                $config = GridFieldConfig_RecordEditor::create();
                $config->addComponents([
                    new GridFieldOrderableRows('SortOrder'),
                    //new GridFieldAddExistingSearchButton(),
                ])
                    ->removeComponentsByType([
                        GridFieldAddExistingAutocompleter::class,
                    ]);

                $faqs = GridField::create(
                    'FAQs',
                    'FAQs',
                    FAQ::get()->sort('SortOrder'),
                    $config
                );
                $fields->addFieldsToTab('Root.FAQs', [
                    $faqs,
                ]);
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return DataList
     */
    public function getFAQList()
    {
        return FAQ::get()->sort('SortOrder');
    }
}
