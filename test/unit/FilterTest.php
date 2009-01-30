<?php
require_once dirname(__FILE__).'/../test_helper.php';

class FilterTest extends PHPUnit_Framework_TestCase 
{
    function testAddRules()
    {
        $expected_xml = '<filter name="test" fullData="true">'.
        '<rule type="actor">me</rule>' .
        '</filter>';

        $rules = array(new Services_Gnip_Rule("actor", "me"));
        $filter = new Services_Gnip_Filter('test', 'true', '', $rules);
        $this->assertEquals($expected_xml, $filter->toXML());
        
        $expected_xml = '<filter name="test" fullData="true">'.
        '<rule type="actor">me</rule>' .
        '<rule type="to">you</rule>' .
        '</filter>';

        $filter->addRules(array(new Services_Gnip_Rule("to", "you")));
        $this->assertEquals($expected_xml, $filter->toXML());
    }

    function testRemoveRules()
    {
        $expected_xml = '<filter name="test" fullData="true">'.
        '<rule type="actor">me</rule>' .
        '<rule type="actor">you</rule>' .
        '</filter>';
        
        $rules = array(new Services_Gnip_Rule("actor", "me"),
                new Services_Gnip_Rule("actor", "you"));
        $filter = new Services_Gnip_Filter('test', 'true', '', $rules);
        $this->assertEquals($expected_xml, $filter->toXML());

        $expected_xml = '<filter name="test" fullData="true">'.
        '<rule type="actor">me</rule>' .
        '</filter>';

        $filter->removeRules(array(new Services_Gnip_Rule("actor", "you")));
        $this->assertEquals($expected_xml, $filter->toXML());
    }



    function testToXmlWithoutpostUrl()
    {
        $expected_xml = '<filter name="test" fullData="true">'.
        '<rule type="actor">me</rule>' .
        '<rule type="actor">you</rule>' .
        '<rule type="actor">bob</rule>' .
        '</filter>';
 
        $rules = array(new Services_Gnip_Rule("actor", "me"), new Services_Gnip_Rule("actor", "you"), new Services_Gnip_Rule("actor", "bob"));

        $f = new Services_Gnip_Filter('test', 'true', '', $rules);

        $this->assertEquals($expected_xml, $f->toXML());	
    }

    function testToXmlWithpostUrl()
    {
         $expected_xml = '<filter name="test" fullData="true">'.
            '<postURL>http://example.com</postURL>' .
            '<rule type="actor">me</rule>' .
            '<rule type="actor">you</rule>' .
            '<rule type="actor">bob</rule>' .
            '</filter>';

        $rules = array(new Services_Gnip_Rule("actor", "me"), 
            new Services_Gnip_Rule("actor", "you"), 
            new Services_Gnip_Rule("actor", "bob"));

        $f = new Services_Gnip_Filter('test', 'true', 'http://example.com', $rules);
        $this->assertEquals($expected_xml, $f->toXML());
    }

    function testFromXmlWithoutpostUrl()
    {
        $xml = '<filter name="test" fullData="true">' .
            '<rule type="actor">me</rule>' .
            '<rule type="actor">you</rule>' .
            '<rule type="actor">bob</rule>' .
            '</filter>';

        $rules = array(new Services_Gnip_Rule("actor", "me"), 
            new Services_Gnip_Rule("actor", "you"), 
            new Services_Gnip_Rule("actor", "bob"));

        $f = Services_Gnip_Filter::fromXml($xml);
        $this->assertEquals("test", $f->name);
        $this->assertEquals("true", $f->fullData);
        $this->assertEquals("", $f->postURL);
        $this->assertEquals($rules, $f->rules);	
    }

    function testFromXmlWithpostUrl()
    {
        $xml = '<filter name="test" fullData="true">' .
            '<postURL>http://example.com</postURL>' .
            '<rule type="actor">me</rule>' .
            '<rule type="actor">you</rule>' .
            '<rule type="actor">bob</rule>' .
            '</filter>';

        $rules = array(new Services_Gnip_Rule("actor", "me"), 
            new Services_Gnip_Rule("actor", "you"), 
            new Services_Gnip_Rule("actor", "bob"));

        $f = Services_Gnip_Filter::fromXml($xml);
        $this->assertEquals("test", $f->name);
        $this->assertEquals("true", $f->fullData);
        $this->assertEquals("http://example.com", $f->postURL);
        $this->assertEquals($rules, $f->rules);	
    }

    function testGetCreateUrl()
    {
        $pubRuleTypes = array();
        $filterRules = array();
        $publisher = new Services_Gnip_Publisher('name', $pubRuleTypes);
        $filter = new Services_Gnip_Filter("myFilter", 'false', '', $filterRules);

        $expected_url = "/publishers/" . $publisher->name . "/filters.xml";
        $this->assertEquals($expected_url, $filter->getCreateUrl($publisher->name));
    }

    function testGetUrl()
    {
        $pubRuleTypes = array();
        $filterRules = array();
        $publisher = new Services_Gnip_Publisher('name', $pubRuleTypes);
        $filter = new Services_Gnip_Filter("myFilter", 'false', '', $filterRules);

        $expected_url = "/publishers/" . $publisher->name ."/filters/" . $filter->name. ".xml";
        $this->assertEquals($expected_url, $filter->getUrl($publisher->name));
    }

    function testGetIndexUrl()
    {
        $pubRuleTypes = array();
        $filterRules = array();
        $publisher = new Services_Gnip_Publisher('name', $pubRuleTypes);
        $filter = new Services_Gnip_Filter("myFilter", 'false', '', $filterRules);

        $expected_url = "/publishers/" . $publisher->name ."/filters.xml";
        $this->assertEquals($expected_url, $filter->getIndexUrl($publisher->name));
    }
}
?>