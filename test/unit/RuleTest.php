 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class RuleTest extends PHPUnit_Framework_TestCase
{
    function testToXml()
    {
        $expected_xml = '<rule type="actor">testActor</rule>';
        $rule = new Services_Gnip_Rule('actor', 'testActor');
        $this->assertEquals($expected_xml, $rule->toXML());
    }

    function testFromXml()
    {
        $xml = '<rule type="actor">testActor</rule>';

        $rule = Services_Gnip_Rule::fromXml($xml);
        $this->assertEquals("actor", $rule->type);
        $this->assertEquals("testActor", $rule->value);
    }

    function testGetCreateUrl()
    {
        $pubRuleTypes = array();
        $filterRules = array();
        $publisher = new Services_Gnip_Publisher('name', $pubRuleTypes);
        $filter = new Services_Gnip_Filter("myFilter", 'false', '', $filterRules);
        $rule = new Services_Gnip_Rule("actor", "you");

        $expected_url = "/publishers/" . $publisher->name . "/filters/" . $filter->name . "/rules.xml";
        $this->assertEquals($expected_url, $rule->getCreateUrl($publisher->name, $filter->name));
    }

    function testGetUrl()
    {
        $pubRuleTypes = array();
        $filterRules = array();
        $publisher = new Services_Gnip_Publisher('name', $pubRuleTypes);
        $filter = new Services_Gnip_Filter("myFilter", 'false', '', $filterRules);
        $rule = new Services_Gnip_Rule("actor", "you");

        $expected_url = "/publishers/" . $publisher->name ."/filters/" . $filter->name ."/rules.xml";
        $this->assertEquals($expected_url, $rule->getUrl($publisher->name, $filter->name));
    }
}