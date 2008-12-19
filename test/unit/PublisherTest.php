<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherTest extends PHPUnit_Framework_TestCase
{
    function testToXml()
    {
        $rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $publisher = new Services_Gnip_Publisher('name', $rule_types);
      
        $this->assertEquals('<publisher name="name"><supportedRuleTypes><type>actor</type></supportedRuleTypes></publisher>', $publisher->toXML());
    }
    
    function testFromXml()
    {
        $publisher = Services_Gnip_Publisher::fromXML(new SimpleXMLElement("<publisher name='bob'><supportedRuleTypes><type>actor</type></supportedRuleTypes></publisher>"));
        $this->assertEquals("bob", $publisher->name);
        $this->assertEquals("actor", $publisher->supported_rule_types[0]->type);
    }

	function testGetIndexUrl(){
		$rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $publisher = new Services_Gnip_Publisher('name', $rule_types);

		$expected_url = '/publishers.xml';
		$this->assertEquals($expected_url, $publisher->getIndexUrl());
	}
	
	function testGetUrl()
    {
		$rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $publisher = new Services_Gnip_Publisher('name', $rule_types);

        $expected_url = '/publishers/'. $publisher->name;
		$this->assertEquals($expected_url, $publisher->getUrl());
    }

	function testGetCreateUrl(){
		$rule_types = array(new Services_Gnip_Rule_Type('actor'));
        $publisher = new Services_Gnip_Publisher('name', $rule_types);

		$expected_url =  '/publishers';
		$this->assertEquals($expected_url, $publisher->getCreateUrl());
	}
	
}
?>