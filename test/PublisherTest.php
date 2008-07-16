<?php
require_once dirname(__FILE__).'/test_helper.php';

class PublisherTest extends PHPUnit_Framework_TestCase {

	function testPublishActivies()
	{
		$publisher = new Publisher('name');
	  
	    $this->assertEquals('<publisher name="name"/>', $publisher->toXML());
	}
}