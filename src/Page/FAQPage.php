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

class FAQPage_Controller extends \PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'view',
    ];

    /**
     * @param HTTPRequest $request
     * @return DBHTMLText
     */
    public function view(HTTPRequest $request)
    {
        $urlSegment = $request->latestParam('ID');

        if (!$object = FAQ::get()->filter('URLSegment', $urlSegment)->first()) {
            return $this->httpError(404, "The FAQ you're looking for doesn't seem to be here.");
        }

        return $this->customise(new ArrayData([
            'Object' => $object,
            'Title' => $object->Title,
            'MetaTags' => $object->MetaTags(),
            'Breadcrumbs' => $object->Breadcrumbs(),
        ]))->renderWith([
            'FAQ',
            'Page',
        ]);
    }
}
