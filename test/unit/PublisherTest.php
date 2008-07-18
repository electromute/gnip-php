<?php
require_once dirname(__FILE__).'/../test_helper.php';

class PublisherTest extends PHPUnit_Framework_TestCase
{
    function testToXml()
    {
        $publisher = new Services_Gnip_Publisher('name');
      
        $this->assertEquals('<publisher name="name"/>', $publisher->toXML());
    }
    
    function testFromXml()
    {
        $publisher = Services_Gnip_Publisher::fromXML(new SimpleXMLElement("<publisher name='bob'/>"));
        $this->assertEquals("bob", $publisher->name);
    }
}
?>