 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class PayloadTest extends PHPUnit_Framework_TestCase
{
    function _testGzipAndBase64EncodeRaw()
    {
        $expected_body = "body";
        $expected_raw = "raw";
        $payload = new Services_Gnip_Payload($expected_body, $expected_raw);
        $this->assertEquals($expected_body, $payload->body);
        $this->assertEquals($expected_raw, gzinflate(base64_decode($payload->raw)));
        $this->assertEquals($expected_raw, $payload->decodedRaw());
    }

    function testNullRaw()
    {
        $expected_body = "body";
        $payload = new Services_Gnip_Payload($expected_body);
        $this->assertEquals($expected_body, $payload->body);
        $this->assertNull($payload->raw);
        $this->assertNull($payload->decodedRaw());
    }

    function testToXml()
    {
		$expected_xml = "<payload><body>body</body></payload>";
        $payload = new Services_Gnip_Payload("body");
        $this->assertEquals($expected_xml, $payload->toXML());
    }

	function testFromXml()
    {
		$xml = "<payload><body>body</body></payload>";
		$payload = Services_Gnip_Payload::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals("body", $payload->body);
        $this->assertNull($payload->raw);        
    }	
}
?>
