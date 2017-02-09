<?php

class FAQTopic extends DataObject
{
    /**
     * @var string
     */
    private static $singular_name = 'FAQ Topic';

    /**
     * @var string
     */
    private static $plural_name = 'FAQ Topics';

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'FAQs' => 'FAQ',
    );

    /**
     * @var string
     */
    private static $default_sort = 'Title';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->ID) {
            $faqs = $fields->dataFieldByName('FAQs');
            $config = $faqs->getConfig();
            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $config->addComponent(new GridFieldAddExistingSearchButton());
            $config->removeComponentsByType('GridFieldAddNewButton');
        }

        return $fields;
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canCreate($member = null)
    {
        return Permission::check('FAQ_CREATE');
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return Permission::check('FAQ_EDIT');
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('FAQ_DELETE');
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canView($member = null)
    {
        return true;
    }
}
