<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("", "");

        $rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $this->publisher = new Services_Gnip_Publisher("", $rule_types);
    }

    public function testGetPublishers()
    {
        $publishers = $this->gnip->getPublishers();
		$names = array();
		foreach ($publishers as $publisher){
			$names[] = $publisher->name;
		}
        assertContains($this->publisher->name, $names);
    }

    public function testGetPublisher()
    {
		$pub = $this->gnip->getPublisher($this->publisher->name);
        $this->assertEquals($pub, $this->publisher);
    }

    public function testUpdatePublisher()
    {
        $pub = $this->gnip->getPublisher($this->publisher->name);
        $this->assertEquals($pub, $this->publisher);        

        $rule_types = array(new Services_Gnip_Rule_Type('to'));
        $pub->addRuleTypes($rule_types);
        $this->gnip->updatePublisher($pub);

        $modified_pub = $this->gnip->getPublisher($pub->name);
        $this->assertEquals($modified_pub->name, $this->publisher->name);
        $this->assertEquals(2, count($modified_pub->supported_rule_types));     

        $modified_pub->removeRuleTypes($rule_types);
        $this->gnip->updatePublisher($modified_pub);

        $original_publisher = $this->gnip->getPublisher($modified_pub->name);
        $this->assertEquals($original_publisher->name, $this->publisher->name);
        $this->assertEquals(1, count($this->publisher->supported_rule_types));
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
		$pub = $this->gnip->getPublisher($this->publisher->name);
        $this->assertEquals($pub, $this->publisher);

        $payload = new Services_Gnip_Payload("body", "raw");

        $activity = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'joe', strval(rand(0, 9999999)), 'web', 'trains,planes,automobiles', 'bob', 'http://example.com', $payload);

        $this->gnip->publish($this->publisher, array($activity));

        $activities = $this->gnip->getPublisherActivities($this->publisher);

        assertContains($activity, $activities);
        $this->assertEquals($activity->payload->decodedRaw(), $activities[1]->payload->decodedRaw());
    }
}
?>