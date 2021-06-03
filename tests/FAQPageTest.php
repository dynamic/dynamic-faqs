<?php

namespace Dynamic\FAQ\Test;

use Dynamic\FAQ\Model\FAQ;
use Dynamic\FAQ\Page\FAQPage;
use Dynamic\FAQ\Page\FAQPageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\DataList;

class FAQPageTest extends FunctionalTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testGetFAQList()
    {
        $page = $this->objFromFixture(FAQPage::class, 'default');
        $this->assertInstanceOf(DataList::class, $page->getFAQList());
    }
}
