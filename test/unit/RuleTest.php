 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class RuleTest extends PHPUnit_Framework_TestCase
{
    function testToXml()
    {
		$expected_xml = '<rule type="actor" value="testActor"/>';

        $rule = new Services_Gnip_Rule('actor', 'testActor');

        $this->assertEquals($expected_xml, $rule->toXML());
    }

	function testFromXml()
    {
		$xml = '<rule type="actor" value="testActor"/>';

		$rule = Services_Gnip_Rule::fromXml(new SimpleXMLElement($xml));
        $this->assertEquals("actor", $rule->type);
		$this->assertEquals("testActor", $rule->value);
    }
}