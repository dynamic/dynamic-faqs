<?php

class FAQTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'dynamic-faqs/tests/fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
    }

    /**
     *
     */
    public function testValidateTitle()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $object->Title = '';
        $this->setExpectedException('ValidationException');
        $object->write();
    }

    /**
     *
     */
    public function testGetTopicNames()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $this->assertEquals($object->getTopicNames(), 'Topic One, Topic Two');
    }

    /**
     *
     */
    public function testGetTopicName()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $this->assertEquals($object->getTopicName(), 'Topic One');
    }

    /**
     *
     */
    public function testGetCustomSearchContext()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $this->assertInstanceOf('SearchContext', $object->getCustomSearchContext());
    }

    /**
     *
     */
    public function testGetParentPage()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $page = $this->objFromFixture('FAQPage', 'default');
        $this->assertEquals($object->getParentPage(), $page);
    }

    /**
     *
     */
    public function testGetViewAction()
    {
        $object = $this->objFromFixture('FAQ', 'one');
        $this->assertEquals($object->getViewAction(), 'view');
    }

    /**
     *
     */
    public function testProvidePermissions()
    {
        $object = $this->objFromFixture('FAQ', 'one');
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
        $object = $this->objFromFixture('FAQ', 'one');

        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canView($admin));

        $member = $this->objFromFixture('Member', 'default');
        $this->assertTrue($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        $object = $this->objFromFixture('FAQ', 'one');

        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canEdit($admin));

        $member = $this->objFromFixture('Member', 'default');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture('FAQ', 'one');

        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canDelete($admin));

        $member = $this->objFromFixture('Member', 'default');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture('FAQ', 'one');

        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canCreate($admin));

        $member = $this->objFromFixture('Member', 'default');
        $this->assertFalse($object->canCreate($member));
    }
}
