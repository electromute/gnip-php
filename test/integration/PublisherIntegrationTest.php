<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("jeremy.lightsmith@gmail.com", "test");
        $this->publisher = new Services_Gnip_Publisher("bob");
    }

    public function testPublishActivities()
    {
        // DATE_ISO8601 gives us 2008-07-15T15:42:47-0700
        // DATE_ATOM    gives us 2008-07-15T15:43:46-07:00 
        $atString = date_create()->format(DATE_ATOM);

        $activity = new Services_Gnip_Activity($atString,'added_friend','foo/bob1');
        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getActivities($this->publisher);
        $this->assertContains($activities, $activity);
    }
}
?>