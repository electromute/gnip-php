 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class ActivityTest extends PHPUnit_Framework_TestCase
{
    function testCanCreateWithEitherDateTimeOrDateTimeString()
    {
        $time = "2008-07-15 00:00:00";
        $a = new Services_Gnip_Activity(new DateTime($time), "uid", "type");
        $b = new Services_Gnip_Activity($time,               "uid", "type");
        $this->assertEquals($a, $b);
    }
    
    function testGenerateXmlWithRequiredFields()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00','uid','type');
        $this->assertEquals('<activity at="2008-07-15T00:00:00-07:00" uid="uid" type="type"/>', $a->toXML());   
    }
    
    function testGenerateXmlWithOptionalGuid()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00','uid','type','guid');
        $this->assertEquals('<activity at="2008-07-15T00:00:00-07:00" uid="uid" type="type" guid="guid"/>', $a->toXML());   
    } 

    function testGenerateXmlWithOptionalPublisher()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00','uid','type','','publisher');
        $this->assertEquals('<activity at="2008-07-15T00:00:00-07:00" uid="uid" type="type" publisher.name="publisher"/>', $a->toXML());    
    }

    function testGenerateXmlWithGuidAndPublisherName()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00','uid', 'type', 'guid', 'publisher');
        $this->assertEquals('<activity at="2008-07-15T00:00:00-07:00" uid="uid" type="type" guid="guid" publisher.name="publisher"/>', $a->toXML());    
    }
    
    function testFromXml()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00','uid', 'type', 'guid', 'publisher');
        $this->assertEquals($a, Services_Gnip_Activity::fromXML(new SimpleXMLElement('<activity at="2008-07-15T00:00:00-07:00" uid="uid" type="type" guid="guid" publisher.name="publisher"/>')));
    }
    
    function testEquals()
    {
        $a = new Services_Gnip_Activity('2008-07-15 00:00:00', 'uid', 'type', 'guid', 'publisher');
        $b = new Services_Gnip_Activity('2008-07-15 00:00:00', 'uid', 'type', 'guid', 'publisher');
        $c = new Services_Gnip_Activity('2008-07-15 00:00:00', 'uid2', 'type', 'guid', 'publisher');
    
        $this->assertEquals($a, $b);
        $this->assertNotEquals($a, $c);
    }
    
}