<?php
require_once dirname(__FILE__).'/../test_helper.php';

class FilterIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->gnip = new Services_Gnip("", "");
		$this->publisher = "";

		$rules = array(new Services_Gnip_Rule("actor", "me"), 
			new Services_Gnip_Rule("actor", "you"), 
			new Services_Gnip_Rule("actor", "bob"));

        $this->filter = new Services_Gnip_Filter(uniqid('apitestfilter'), 'false', '', '', $rules);

        $this->gnip->createFilter($this->publisher, $this->filter);
    }
    
    public function tearDown()
    {
        $this->gnip->deleteFilter($this->publisher, $this->filter);
    }
    
    public function testCanCreateFilter()
    {
		$retrievedFilter = $this->gnip->getFilter($this->publisher, $this->filter->name);
        
        $this->assertContains("bob", $retrievedFilter->toXML());
    }
    
    public function testCanUpdateFilter()
    {
		$rule = new Services_Gnip_Rule("actor", "tom");
        $this->filter->rules[] = $rule;
        
        $this->gnip->updateFilter($this->publisher, $this->filter);

		$retrievedFilter = $this->gnip->getFilter($this->publisher, $this->filter->name);
        
        $this->assertContains("tom", $retrievedFilter->toXML());
    }
    
    public function testCanGetNotifications()
    {
        $this->gnip->getFilterNotifications($this->publisher, $this->filter);
    }
}
?>