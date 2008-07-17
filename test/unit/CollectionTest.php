 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class CollectionTest extends PHPUnit_Framework_TestCase 
{
	
  function testGenerateXmlWithRequiredFields()
  {
	$c = new Services_Gnip_Collection('name');
	$this->assertEquals('<collection name="name"/>', $c->toXML());	
  }

  function testGenerateXmlWithOptionalFields()
  {
	$c = new Services_Gnip_Collection('name','http://post-endpoint.example.com');
	$this->assertEquals('<collection name="name" postUrl="http://post-endpoint.example.com"/>', $c->toXML());	
  }

  function testCollectionHasUidList()
  {
	$c = new Services_Gnip_Collection('name');
    $c->uids[] = new Services_Gnip_Uid('bob', 'bob-publisher');
	$this->assertEquals('<collection name="name"><uid name="bob" publisher.name="bob-publisher"/></collection>', $c->toXML());		
  }

  function testCreateCollectionFromResponse()
  {
	$xml = '<collection name="name"><uid name="bob" publisher.name="bob-publisher"/></collection>';
	$this->assertEquals($xml, Services_Gnip_Collection::fromXml(new SimpleXMLElement($xml))->toXML());
  }

  function testCreateCollectionFromResponseWithPostUrl()
  {
	$xml = '<collection name="name" postUrl="yourmom.com"><uid name="bob" publisher.name="bob-publisher"/></collection>';
	$this->assertEquals($xml, Services_Gnip_Collection::fromXml(new SimpleXMLElement($xml))->toXML());
  }
}