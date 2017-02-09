<?php

class FAQPage extends Page
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

class FAQPage_Controller extends Page_Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'view',
    ];

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText
     */
    public function view(SS_HTTPRequest $request)
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
