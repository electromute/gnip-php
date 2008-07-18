<?php
require_once dirname(__FILE__).'/../test_helper.php';

class CollectionIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("jeremy.lightsmith@gmail.com", "test");
        $this->collection = new Services_Gnip_Collection(uniqid('apitestcollection'));
        $this->collection->uids = array(new Services_Gnip_Uid("me", "digg"));

        $this->gnip->createCollection($this->collection);
    }
    
    public function tearDown()
    {
        $this->gnip->deleteCollection($this->collection);
    }
    
    public function testCanCreateCollection()
    {
        $this->assertEquals($this->collection, $this->gnip->getCollection($this->collection->name));
    }
    
    public function testCanUpdateCollection()
    {
        $this->collection->uids[] = new Services_Gnip_Uid("you", "flickr");
        
        $this->gnip->updateCollection($this->collection);
        
        $this->assertEquals($this->collection, $this->gnip->getCollection($this->collection->name));
    }
    
    public function testCanGetActivities()
    {
        $this->gnip->getActivities($this->collection);
    }
}
?>