<?php

namespace Dynamic\FAQ\Page;

use Dynamic\FAQ\Model\FAQ;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;

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
}
