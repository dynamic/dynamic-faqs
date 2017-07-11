<?php

class FAQ extends DataObject implements PermissionProvider, Dynamic\ViewableDataObject\VDOInterfaces\ViewableDataObjectInterface
{
    /**
     * @var string
     */
    private static $singular_name = 'FAQ';

    /**
     * @var string
     */
    private static $plural_name = 'FAQs';

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'Popularity' => 'Int',
        'Keywords' => 'Text',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Topics' => 'FAQTopic',
    );

    /**
     * @var array
     */
    private static $many_many_extraFields = array(
        'Topics' => array(
            'Sort' => 'Int',
        ),
    );

    /**
     * @var string
     */
    private static $default_sort = 'Popularity DESC, Title';

    /**
     * @var array
     */
    private static $extensions = [
        'Heyday\VersionedDataObjects\VersionedDataObject',
    ];

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'Popularity' => 'Popularity',
    );

    /**
     * @var array
     */
    private static $searchable_fields = array(
        'Title' => [
            'title' => 'Title',
        ],
        'Content' => [
            'title' => 'Content',
        ],
        'Topics.ID' => [
            'title' => 'Topic',
        ],
        'Keywords' => [
            'title' => 'Keywords'
        ],
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Popularity',
            'Topics',
        ]);

        $fields->insertBefore(TextField::create('Keywords', 'Keywords'), 'Content');

        if ($this->ID) {
            // Topics
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldOrderableRows('Sort'));
            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $config->addComponent(new GridFieldAddExistingSearchButton());
            $topics = $this->Topics()->sort('Sort');
            $topicsField = GridField::create('Topics', 'Topics', $topics, $config);

            $fields->addFieldsToTab('Root.Topics', array(
                $topicsField,
            ));
        }

        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();
        if (!$this->Title) {
            $result->error('A Title is required before you can save');
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getTopicNames()
    {
        if ($this->Topics()->exists()) {
            $list = '';
            $ct = 1;
            foreach ($this->Topics() as $topic) {
                $list .= $topic->Title;
                if ($ct < $this->Topics()->Count()) {
                    $list .= ', ';
                }
                ++$ct;
            }

            return $list;
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getTopicName()
    {
        return $this->Topics()->first()->getTitle();
    }

    /**
     * @return SearchContext
     */
    public function getCustomSearchContext()
    {
        $fields = $this->scaffoldSearchFields(array(
            'restrictFields' => array('Title', 'Topics.ID')
        ));

        $filters = array(
            'Title' => new PartialMatchFilter('Title'),
            'Topics.ID' => new ExactMatchFilter('Topics.ID'),
        );

        return new SearchContext(
            $this->class,
            $fields,
            $filters
        );
    }

    /**
     * @return string
     */
    public function getParentPage()
    {
        return FAQPage::get()->first();
    }

    /**
     * @return string
     */
    public function getViewAction()
    {
        return 'view';
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return array(
            'FAQ_EDIT' => 'Edit a FAQ',
            'FAQ_DELETE' => 'Delete a FAQ',
            'FAQ_CREATE' => 'Create a FAQ',
        );
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canCreate($member = null)
    {
        return Permission::check('FAQ_CREATE', 'any', $member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return Permission::check('FAQ_EDIT', 'any', $member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('FAQ_DELETE', 'any', $member);
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
