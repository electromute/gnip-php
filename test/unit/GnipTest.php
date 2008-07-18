<?php
require_once dirname(__FILE__).'/../test_helper.php';

class MockHelper
{
    function doHttpGet($url)
    {
        return $this->call('get', $url);
    }
    
    function doHttpPost($url, $value)
    {
        return $this->call('post', $url, $value);
    }
    
    function doHttpDelete($url)
    {
        $this->call('delete', $url);
    }
    
    /**
     * options:
     *   value: 
     *   return: 
     */
    function expect($method, $url, $options = array()) 
    {
        $this->call = $options;
        $this->call['method'] = $method;
        $this->call['url'] = $url;
    }
    
    private function call($method, $url, $value = null)
    {
        PHPUnit_Framework_Assert::assertEquals($this->call['method'], $method);
        PHPUnit_Framework_Assert::assertEquals($this->call['url'], $url);
        PHPUnit_Framework_Assert::assertEquals($this->call['value'], $value);
        return $this->call['and_return'];
    }
}

class GnipTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->gnip = new Services_Gnip("user", "pass");
        $this->gnip->helper = $this->helper = new MockHelper();
    }

    function testGetPublishers()
    {
        $this->helper->expect('get', '/publishers.xml', 
                              array('and_return' => "<publishers><publisher name='bob'/></publishers>"));
        $publishers = $this->gnip->getPublishers();
        $this->assertEquals("bob", $publishers[0]->name);
    }

    function testGetPublisher()
    {
        $this->helper->expect('get', '/publishers/bob.xml', 
                              array('and_return' => "<publisher name='bob'/>"));
        $publisher = $this->gnip->getPublisher('bob');
        $this->assertEquals("bob", $publisher->name);
    }

    function testPublish()
    {
        $a = new Services_Gnip_Activity(new DateTime(), "uid", "type");

        $this->helper->expect('post', '/publishers/bob/activity.xml', 
                              array('value' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                                               "<activities>".$a->toXML()."</activities>\n"));
                              
        $this->gnip->publish(new Services_Gnip_Publisher('bob'), array($a));
    }

    function testGetCurrentActivities()
    {
        $a = new Services_Gnip_Activity(new DateTime(), "uid", "type");

        $this->helper->expect('get', '/publishers/digg/activity/current.xml', 
                              array('and_return' => "<activities>".$a->toXML()."</activities>"));
                              
        $activities = $this->gnip->getActivities(new Services_Gnip_Publisher('digg'));
        $this->assertEquals($a, $activities[0]);
    }
    
    function testCreateCollection()
    {
        $c = new Services_Gnip_Collection("mycollection");
        $c->uids = array(new Services_Gnip_Uid("me", "digg"));

        $this->helper->expect('post', '/collections.xml', 
                              array('value' => $c->toXML()));
                              
        $this->gnip->createCollection($c);
    }

    function testDeleteCollection()
    {
        $c = new Services_Gnip_Collection("mycollection");

        $this->helper->expect('delete', '/collections/mycollection.xml');
                              
        $this->gnip->deleteCollection($c);
    }
}
?>