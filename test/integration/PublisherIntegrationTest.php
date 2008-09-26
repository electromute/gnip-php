<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("", "");
        $this->publisher = new Services_Gnip_Publisher("");
    }
    
    public function testGetPublishers()
    {
        $publishers = $this->gnip->getPublishers();
        assertContains($this->publisher, $publishers);
    }

    public function testGetPublisher()
    {
        $this->assertEquals($this->gnip->getPublisher(""), $this->publisher);
    }

    public function testPublishAndGetActivities()
    {
		$activity = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'sally', strval(rand(0, 9999999)), 'web', 'trains,planes,automobiles', 'bob', 'http://example.com');

        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getPublisherActivities($this->publisher);
        assertContains($activity, $activities);

        $activities = $this->gnip->getPublisherActivities($this->publisher, time());
        assertContains($activity, $activities);
    }

	public function testGetNotifications()
    {
		$activity = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'sally', strval(rand(0, 9999999)), 'web', 'trains,planes,automobiles', 'bob', 'http://example.com');

        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getPublisherNotifications($this->publisher);
        assertContains($activity, $activities);

        $activities = $this->gnip->getPublisherNotifications($this->publisher, time());
        assertContains($activity, $activities);
    }
}
?>