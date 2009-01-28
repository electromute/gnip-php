 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class PayloadTest extends PHPUnit_Framework_TestCase
{
    function testGzipAndBase64EncodeRaw()
    {
        $expected_raw = "raw";
        $payload = new Services_Gnip_Payload($expected_raw, "title", "body", array("mediaURL" => "http://gnipcentral.com", "type" => "link"));
        $this->assertEquals($expected_raw, $payload->decodedRaw());
    }

    function testNullStuff()
    {
        $expected_raw = "raw";
        $payload = new Services_Gnip_Payload($expected_raw);
        $this->assertEquals($expected_raw, $payload->decodedRaw());
        $this->assertNull($payload->title);
        $this->assertNull($payload->body);
        $this->assertNull($payload->mediaURL);
    }

    function testToXml()
    {
        $doc = new GnipDOMDocument();
        $root = $doc->createElement('activity');
        $doc->appendChild($root);
        
        $expected_xml = '<activity><payload><title>title</title><body>body</body><mediaURL height="200" width="200" duration="107">http://gnipcentral.com</mediaURL><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload></activity>';

        $payload = new Services_Gnip_Payload("raw", "title", "body", array("mediaURL" => "http://gnipcentral.com", "height" => "200", "width" => "200", "duration" => "107"));
        $payload->toXML($doc, $root);
        $this->assertEquals($expected_xml, $doc->asXML());
    }

    function testToXmlUnbound()
    {
        $mediaURLS = array(
            array("mediaURL" => "http://gnipcentral.com", "type" => "image"),
            array("mediaURL" => "http://flickr.com/tour", "type" => "video")
            );
        $doc = new GnipDOMDocument();
        $root = $doc->createElement('activity');
        $doc->appendChild($root);
        
        $expected_xml = '<activity><payload><title>title</title><body>body</body>'.
            '<mediaURL type="image">http://gnipcentral.com</mediaURL>'.
            '<mediaURL type="video">http://flickr.com/tour</mediaURL>'.
            '<raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload></activity>';

        $payload = new Services_Gnip_Payload("raw", "title", "body", $mediaURLS);
        $payload->toXML($doc, $root);
        $this->assertEquals($expected_xml, $doc->asXML());
    }

    function testFromXmlNullStuff()
    {
        $xml = '<payload><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>';
        $payload = Services_Gnip_Payload::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals("raw", $payload->decodedRaw());        
    }

    function testFromXml()
    {
        $xml = '<payload><title>title</title><body>body</body><mediaURL height="200" width="200" duration="107" mimeType="video/quicktime"  type="movie">http://www.gnipcentral.com</mediaURL><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>';
        $payload = Services_Gnip_Payload::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals("title", $payload->title);
        $this->assertEquals("body", $payload->body);
        $this->assertContains(array("mediaURL" => "http://www.gnipcentral.com", "height" => "200", "width" => "200", "duration" => "107", "mimeType" => "video/quicktime", "type" => "movie"), $payload->mediaURL);
        $this->assertEquals("raw", $payload->decodedRaw());        
    }
    
    function testFromXmlUnbound()
    {
        $xml = '<payload><title>title</title><body>body</body><mediaURL type="movie" mimeType="video/quicktime">http://www.gnipcentral.com</mediaURL>'.
            '<mediaURL type="graphic" mimeType="image/png">http://flickr.com/tour</mediaURL>'.
            '<raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>';
        $payload = Services_Gnip_Payload::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals("title", $payload->title);
        $this->assertEquals("body", $payload->body);
        $this->assertContains(array("mediaURL" => "http://www.gnipcentral.com", "type" => "movie", "mimeType" => "video/quicktime"), $payload->mediaURL);
        $this->assertContains(array("mediaURL" => "http://flickr.com/tour", "type" => "graphic", "mimeType" => "image/png"), $payload->mediaURL);
        $this->assertEquals("raw", $payload->decodedRaw());        
    }

}
?>
