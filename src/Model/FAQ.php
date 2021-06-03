<?php

namespace Dynamic\FAQ\Model;

use Dynamic\FAQ\Page\FAQPage;
use Dynamic\ViewableDataObject\VDOInterfaces\ViewableDataObjectInterface;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class FAQ extends DataObject implements PermissionProvider, ViewableDataObjectInterface
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
        'SortOrder' => 'Int',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Topics' => FAQTopic::class,
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
        Versioned::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'TopicNames' => 'Topics',
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
    );

    /**
     * @var string
     */
    private static $table_name = "FAQ";

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'SortOrder',
            ]);

            if ($this->ID) {
                // Topics
                $config = GridFieldConfig_RelationEditor::create();
                $config->addComponent(new GridFieldOrderableRows('Sort'));
                $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
                $config->addComponent(new GridFieldAddExistingSearchButton());
                $topics = $this->Topics()->sort('Sort');
                $topicsField = GridField::create('Topics', 'Topics', $topics, $config);

                $fields->addFieldsToTab('Root.Topics', array(
                    $topicsField,
                ));
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();
        if (!$this->Title) {
            $result->addError('A Title is required before you can save');
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
    public function canCreate($member = null, $context = [])
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
