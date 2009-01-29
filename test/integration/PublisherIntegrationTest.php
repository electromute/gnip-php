<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        // You'll need to edit this section and fill in all relevant info
        // You'll need to scope this to your namespace, "my"
        // Make sure the supported rule types match your own, make sure its only actor
        $this->gnip = new Services_Gnip("", "");
        $this->scope = "my";
        $rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $this->publisher = new Services_Gnip_Publisher("", $rule_types);
    }

    public function testGetPublishers()
    {
        $publishers = $this->gnip->getAllPublishers("my");
        $names = array();
        foreach ($publishers as $publisher){
            $names[] = $publisher->name;
        }
        assertContains($this->publisher->name, $names);
    }

    public function testGetPublisher()
    {
        $pub = $this->gnip->getPublisher($this->publisher->name, $this->scope);
        $this->assertEquals($pub, $this->publisher);
    }

    public function testUpdatePublisher()
    {
        $pub = $this->gnip->getPublisher($this->publisher->name, $this->scope);
        $this->assertEquals($pub, $this->publisher);        

        $rule_types = array(new Services_Gnip_Rule_Type('to'));
        $pub->addRuleTypes($rule_types);
        $this->gnip->updatePublisher($pub, $this->scope);

        $modified_pub = $this->gnip->getPublisher($pub->name, $this->scope);
        $this->assertEquals($modified_pub->name, $this->publisher->name);
        $this->assertEquals(2, count($modified_pub->supported_rule_types));     

        $modified_pub->removeRuleTypes($rule_types);
        $this->gnip->updatePublisher($modified_pub, $this->scope);

        $original_publisher = $this->gnip->getPublisher($modified_pub->name, $this->scope);
        $this->assertEquals($original_publisher->name, $this->publisher->name);
        $this->assertEquals(1, count($this->publisher->supported_rule_types));
    }

	public function testGetNotifications()
    {
        $activity = new Services_Gnip_Activity(
                    "2008-07-02T11:16:16+00:00", 
                    "upload", 
                    strval(rand(0, 9999999)), 
                    "http://www.gnipcentral.com", 
                    null,
                    null, 
                    null, 
                    null, 
                    null, 
                    null, 
                    null, 
                    null, 
                    null);

        $this->gnip->publish($this->publisher, array($activity), $this->scope);
        
        $activities = $this->gnip->getPublisherNotifications($this->publisher, time(), $this->scope);
        $this->assertEquals(array($activity), $activities);
        
        $activities = $this->gnip->getPublisherNotifications($this->publisher, "current", $this->scope);
        $this->assertEquals(array($activity), $activities);
    }

    public function testGetActivitiesWithPayload()
    {
        $pub = $this->gnip->getPublisher($this->publisher->name, $this->scope);
        $this->assertEquals($pub, $this->publisher);
        
        $place = array(new Services_Gnip_Place("38.2638886 -106.126131", 5280, null, "city", "Boulder", null));
        $payload = new Services_Gnip_Payload("raw", "title", "body", array(array("mediaURL"=>"http://www.flickr.com/tour", "type" => "image", "mimeType" => "image/png"), array("mediaURL" => "http://www.gnipcentral.com/login", "type" => "movie", "mimeType" => "video/quicktime")));

        $activity = new Services_Gnip_Activity("2008-07-02T11:16:16+00:00", "upload", strval(rand(0, 9999999)), "http://www.gnipcentral.com", array(array('source' => 'sms')), array(array('keyword' => 'ping'),array('keyword' => 'pong')), $place, array(array('actor' => 'bob')), array(array("destinationURL" => "http://somewhere.com", "metaURL" => "http://somewhere.com/someplace")), array(array('tag'=>'pong')), array(array('to'=>'sally', 'metaURL' => 'http://gnipcentral.com/users/sally')), null, $payload);

        $this->gnip->publish($this->publisher, array($activity), $this->scope);

        $activities = $this->gnip->getPublisherActivities($this->publisher, time(), $this->scope);

        assertContains($activity, $activities);
        $this->assertEquals($activity->payload->decodedRaw(), $activities[1]->payload->decodedRaw());
    }
}
?>