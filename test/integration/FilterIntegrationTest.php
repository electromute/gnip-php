<?php
require_once dirname(__FILE__).'/../test_helper.php';

class FilterIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        //edit the following with your relevant data.
        
        $this->gnip = new Services_Gnip("", "");
        $this->scope = "my"; // publisher must be owned by you
        $this->pubName = ""; //string of your publisher
        $this->publisher = new Services_Gnip_Publisher($this->pubName, "actor");
        //end editable section

        $rules = array(new Services_Gnip_Rule("actor", "me"), 
            new Services_Gnip_Rule("actor", "you"), 
            new Services_Gnip_Rule("actor", "bob"));

        $this->filter = new Services_Gnip_Filter(uniqid('apitestfilter'), 'false', '', $rules);

        $this->gnip->createFilter($this->pubName, $this->filter, $this->scope);
    }
    
    public function tearDown()
    {
        $this->gnip->deleteFilter($this->pubName, $this->filter, $this->scope);
    }
    
    public function testCanCreateFilter()
    {
        $retrievedFilter = $this->gnip->getFilter($this->pubName, $this->filter->name, $this->scope);
        
        $this->assertContains("bob", $retrievedFilter->toXML());
    }
    
    public function testCanUpdateFilter()
    {
        $rule = new Services_Gnip_Rule("actor", "tom");
        $this->filter->rules[] = $rule;
        
        $this->gnip->updateFilter($this->pubName, $this->filter, $this->scope);

        $retrievedFilter = $this->gnip->getFilter($this->pubName, $this->filter->name, $this->scope);
        
        $this->assertContains("tom", $retrievedFilter->toXML());
    }
    
    public function testCanGetNotifications()
    {
        $this->gnip->getFilterNotifications($this->pubName, $this->filter, "current", $this->scope);
    }
}
?>