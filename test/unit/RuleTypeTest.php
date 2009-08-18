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
    
    
    /**
    * @expectedException PHPUnit_Framework_Error
    */
    function testToXmlValidateSchemaError()
    {
        // blah is not in the schema
        $expected_xml = '<publisher name="test"><supportedRuleTypes><type>action</type><type>actor</type><type>tag</type><type>to</type><type>regarding</type><type>source</type><type>blah</type></supportedRuleTypes></publisher>';
        $doc = new DOMDocument();
        $doc->loadXML($expected_xml);
        $doc->saveXML();        
        $doc->schemaValidate(dirname(__FILE__) . '../../../src/Services/Gnip/gnip.xsd');
    }
    

    function xtestToXmlValidateSchemaLive()
    {
        // makes sure the schema documents match
        $expected_xml = '<publisher name="test"><supportedRuleTypes><type>action</type><type>actor</type><type>tag</type><type>to</type><type>regarding</type><type>source</type><type>keyword</type></supportedRuleTypes></publisher>';
        $doc = new DOMDocument();
        $doc->loadXML($expected_xml);
        $doc->saveXML();        
        $doc->schemaValidate('https://api-v21.gnip.com/schema/gnip.xsd');
    }
    
    function testToXmlValidateSchemaLive()
    {
        // makes sure the schema documents match
        $local_doc = file_get_contents(dirname(__FILE__) . '../../../src/Services/Gnip/gnip.xsd');
        $remote_doc = file_get_contents('https://api-v21.gnip.com/schema/gnip.xsd');
        $this->assertEquals($local_doc, $remote_doc);
    }

    function testFromXml()
    {
        $xml = '<type>actor</type>';

        $rule_type = Services_Gnip_Rule_Type::fromXml($xml);
        $this->assertEquals("actor", $rule_type->type);
    }
}