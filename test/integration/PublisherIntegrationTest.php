<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("jeremy.lightsmith@gmail.com", "test");
        $this->publisher = new Services_Gnip_Publisher("bob");
    }
    
    public function testGetPublishers()
    {
        $publishers = $this->gnip->getPublishers();
        assertContains($this->publisher, $publishers);
    }

    public function testGetPublisher()
    {
        $this->assertEquals($this->gnip->getPublisher("bob"), $this->publisher);
    }

    public function testPublishActivities()
    {
        $activity = new Services_Gnip_Activity(new DateTime(),'added_friend','foo/bob1');
        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getActivities($this->publisher);
        assertContains($activity, $activities);

        $activities = $this->gnip->getActivities($this->publisher, time());
        assertContains($activity, $activities);
    }
}
?>