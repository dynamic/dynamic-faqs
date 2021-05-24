<?php

namespace Dynamic\FAQ\Test;

use Dynamic\FAQ\Model\FAQ;
use Dynamic\FAQ\Page\FAQPage;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;

class FAQTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testValidateTitle()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $object->Title = '';
        $this->setExpectedException(ValidationException::class);
        $object->write();
    }

    /**
     *
     */
    public function testGetTopicNames()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $this->assertEquals($object->getTopicNames(), 'Topic One, Topic Two');
    }

    /**
     *
     */
    public function testGetTopicName()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $this->assertEquals($object->getTopicName(), 'Topic One');
    }

    /**
     *
     */
    public function testGetCustomSearchContext()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $this->assertInstanceOf(SearchContext::class, $object->getCustomSearchContext());
    }

    /**
     *
     */
    public function testGetParentPage()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $page = $this->objFromFixture(FAQPage::class, 'default');
        $this->assertEquals($object->getParentPage(), $page);
    }

    /**
     *
     */
    public function testGetViewAction()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $this->assertEquals($object->getViewAction(), 'view');
    }

    /**
     *
     */
    public function testProvidePermissions()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');
        $expected = array(
            'FAQ_EDIT' => 'Edit a FAQ',
            'FAQ_DELETE' => 'Delete a FAQ',
            'FAQ_CREATE' => 'Create a FAQ',
        );
        $this->assertEquals($object->providePermissions(), $expected);
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');

        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canView($admin));

        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertTrue($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');

        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canEdit($admin));

        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');

        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canDelete($admin));

        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture(FAQ::class, 'one');

        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canCreate($admin));

        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canCreate($member));
    }
}
