<?php

class FAQPageTest extends FunctionalTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'dynamic-faqs/tests/fixtures.yml';
    
    public function testView()
    {
        /*
        $object = $this->objFromFixture('FAQ', 'one');
        $page = $this->objFromFixture('FAQPage', 'default');
        $controller = new FAQPage_Controller($object);
        $reqeust = new SS_HTTPRequest('GET', $object->Link());
        $this->assertInstanceOf('HTMLText', $controller->view($reqeust));
        */
    }
}