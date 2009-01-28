 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class RuleTypeTest extends PHPUnit_Framework_TestCase
{
    function testToXml()
    {
        $expected_xml = '<type>actor</type>';

        $rule = new Services_Gnip_Rule_Type('actor');

        $this->assertEquals($expected_xml, $rule->toXML());
    }

    function testFromXml()
    {
        $xml = '<type>actor</type>';

        $rule_type = Services_Gnip_Rule_Type::fromXml(new SimpleXMLElement($xml));
        $this->assertEquals("actor", $rule_type->type);
    }
}