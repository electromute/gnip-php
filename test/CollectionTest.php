 <?php
require_once dirname(__FILE__).'/test_helper.php';

class CollectionTest extends PHPUnit_Framework_TestCase {
	
  function testGenerateXmlWithRequiredFields(){
	$c = new Collection('name');
	$this->assertEquals('<collection name="name"/>', $c->toXML());	
  }
  function testGenerateXmlWithOptionalFields(){
	$c = new Collection('name','http://post-endpoint.example.com');
	$this->assertEquals('<collection name="name" postUrl="http://post-endpoint.example.com"/>', $c->toXML());	
  }

}