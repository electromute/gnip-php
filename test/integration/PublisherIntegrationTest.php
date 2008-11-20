<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("", "");

        $rule_types = array();
        $rule_types[0] = new Services_Gnip_Rule_Type('actor');

        $this->publisher = new Services_Gnip_Publisher("", $rule_types);
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

	public function testGetNotifications()
    {
		$activity = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'sally', strval(rand(0, 9999999)), 'web', 'trains,planes,automobiles', 'bob', 'http://example.com');

        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getPublisherNotifications($this->publisher);
        assertContains($activity, $activities);

        $activities = $this->gnip->getPublisherNotifications($this->publisher, time());
        assertContains($activity, $activities);
    }

    public function testGetActivitiesWithPayload()
    {
        $this->assertEquals($this->gnip->getPublisher("gniptest"), $this->publisher);

        $payload = new Services_Gnip_Payload("body", "raw");

        $activity = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'joe', strval(rand(0, 9999999)), 'web', 'trains,planes,automobiles', 'bob', 'http://example.com', $payload);

        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getPublisherActivities($this->publisher);

        assertContains($activity, $activities);
        $this->assertEquals($activity->payload->decodedRaw, $activities[1]->payload->decodedRaw);
    }
}
?>