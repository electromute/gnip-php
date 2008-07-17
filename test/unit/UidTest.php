 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class UidTest extends PHPUnit_Framework_TestCase
{
	function testUidXmlWithRequiredParams()
	{
		$uid = new Services_Gnip_Uid('name', 'publisher.name');
		$this->assertEquals('<uid name="name" publisher.name="publisher.name"/>', $uid->toXML());
	}
}