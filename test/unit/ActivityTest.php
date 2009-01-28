 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class ActivityTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->at = "2008-07-02T11:16:16+00:00";
        $this->action = "post";
        $this->activityID = "yk994589klsl";
        $this->URL = "http://www.gnipcentral.com";
        $this->source = array(array("source" => "web"));
        $this->sourceArray = array(
                            array("source" => "web"), 
                            array("source" => "sms")
                            );
        $this->keyword = array(array("keyword" => "ping"));
        $this->keywordArray = array(
                            array("keyword" => "ping"), 
                            array("keyword" => "pong")
                            );
        $this->place = array(new Services_Gnip_Place("45.256 -71.92", null, null, null, null, null));
        $this->placeArray = array(
                            new Services_Gnip_Place("45.256 -71.92", null, null, null, null, null),
                            new Services_Gnip_Place("22.778 -54.998", 5280, 3, "City", "Boulder", null),
                            new Services_Gnip_Place("77.900 - 23.998")
                            );
        $this->actor = array(array("actor" => "Joe",
                                "metaURL" => "http://www.gnipcentral.com/users/joe",
                                "uid" => "1222"));
        $this->actorArray = array(
                            array("actor" => "Joe",
                                "metaURL" => "http://www.gnipcentral.com/users/joe",
                                "uid" => "1222"),
                            array(
                                "actor" => "Bob",
                                "metaURL" => "http://www.gnipcentral.com/users/bob"),
                            array(
                                "actor" => "Susan",
                                "uid" => "1444")
                            );
        $this->destinationURL = array(array("destinationURL" => "http://somewhere.com",
                                        "metaURL" => "http://somewhere.com/someplace"));
        $this->destinationURLArray = array(
                            array("destinationURL" => "http://somewhere.com",
                                "metaURL" => "http://somewhere.com/someplace"),
                            array("destinationURL" => "http://flickr.com")
                                    );
        $this->tag = array(array("tag" => "horses",
                            "metaURL" => "http://gnipcentral.com/tags/horses"));
        $this->tagArray = array(
                            array("tag" => "horses",
                                "metaURL" => "http://gnipcentral.com/tags/horses"),
                            array("tag" => "cows")
                        );
        $this->to = array(array("to" => "Mary",
                        "metaURL" => "http://gnipcentral.com/users/mary"));
        $this->toArray = array(
                array("to" => "Mary",
                    "metaURL" => "http://gnipcentral.com/users/mary"),
                array("to" => "James")
                );
        $this->regardingURL = array(array("regardingURL" => "http://blogger.com/users/posts/mary",
                            "metaURL" => "http://blogger.com/users/mary"));
        $this->regardingURLArray = array(
                            array("regardingURL" => "http://blogger.com/users/posts/mary",
                                "metaURL" => "http://blogger.com/users/mary"),
                            array("regardingURL" => "http://blogger.com/users/posts/james")
                            );
        $this->payload = new Services_Gnip_Payload("raw");
        $this->payloadArray = new Services_Gnip_Payload("raw", "title", "body", array(array("mediaURL"=>"http://www.flickr.com/tour", "type" => "image", "mimeType" => "image/png"), array("mediaURL" => "http://www.gnipcentral.com/login", "type" => "movie", "mimeType" => "video/quicktime")));

    }
    
    function testValidateSchema(){
        $a = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->source, $this->keyword, null, $this->actor, $this->destinationURL, $this->tag, $this->to, $this->regardingURL, null);
        
        $d = new GnipDOMDocument();
        $d->loadXML($a->toXML()); 
        $status = $d->schemaValidate(dirname(__FILE__) . '../../../src/Services/Gnip/gnip.xsd'); 
        $this->assertEquals($status, true);
    }
    
    function testValidatePayloadSchema(){
        $a = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->source, $this->keyword, null, $this->actor, $this->destinationURL, $this->tag, $this->to, $this->regardingURL, array($this->payload, $this->payloadArray));
        
        $d = new GnipDOMDocument();
        $d->loadXML($a->toXML()); 
        $status = $d->schemaValidate(dirname(__FILE__) . '../../../src/Services/Gnip/gnip.xsd'); 
        $this->assertEquals($status, true);
    }
    
    function testToXml()
    {
        $expected_xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<activityID>yk994589klsl</activityID>'.
            '<URL>http://www.gnipcentral.com</URL>'.
            '<source>web</source>'.
            '<keyword>ping</keyword>'.
            '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
            '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
            '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
            '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
            '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
            '</activity>';
        $a = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->source, $this->keyword, null, $this->actor, $this->destinationURL, $this->tag, $this->to, $this->regardingURL, null);
            $this->assertEquals($expected_xml, $a->toXML());
    }

    function testToXmlNulls()
    {
        $expected_xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '</activity>';
        $a = new Services_Gnip_Activity($this->at, $this->action);
        $this->assertEquals($expected_xml, $a->toXML());
    }

    function testToXmlWithPlace()
    {
        $expected_xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<activityID>yk994589klsl</activityID>'.
            '<URL>http://www.gnipcentral.com</URL>'.
            '<source>web</source>'.
            '<keyword>ping</keyword>'.
            '<place><point>45.256 -71.92</point></place>'.
            '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
            '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
            '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
            '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
            '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
            '</activity>';
        $a = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->source, $this->keyword, $this->place, $this->actor, $this->destinationURL, $this->tag, $this->to, $this->regardingURL, null);
        $this->assertEquals($expected_xml, $a->toXML());
    }

    function testToXmlWithPayload()
    {
        $expected_xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<activityID>yk994589klsl</activityID>'.
            '<URL>http://www.gnipcentral.com</URL>'.
            '<source>web</source>'.
            '<keyword>ping</keyword>'.
            '<place><point>45.256 -71.92</point></place>'.
            '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
            '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
            '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
            '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
            '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
            '<payload><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>'.
            '</activity>';
        $a = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->source, $this->keyword, $this->place, $this->actor, $this->destinationURL, $this->tag, $this->to, $this->regardingURL, $this->payload);
        $this->assertEquals($expected_xml, $a->toXML());
    }

    function testToXMLWithUnbounds()
    {
        $expected_xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<activityID>yk994589klsl</activityID>'.
                '<URL>http://www.gnipcentral.com</URL>'.
                '<source>web</source>'.
                '<source>sms</source>'.
                '<keyword>ping</keyword>'.
                '<keyword>pong</keyword>'.
                '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
                '<actor metaURL="http://www.gnipcentral.com/users/bob">Bob</actor>'.
                '<actor uid="1444">Susan</actor>'.
                '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
                '<destinationURL>http://flickr.com</destinationURL>'.
                '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
                '<tag>cows</tag>'.
                '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
                '<to>James</to>'.
                '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
                '<regardingURL>http://blogger.com/users/posts/james</regardingURL>'.
                '</activity>';
        $d = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->sourceArray, $this->keywordArray, null, $this->actorArray, $this->destinationURLArray, $this->tagArray, $this->toArray, $this->regardingURLArray, null);
        
        $this->assertEquals($expected_xml, $d->toXML());
    }

    function testToXMLWithPlaceUnbounds()
    {
        $expected_xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<activityID>yk994589klsl</activityID>'.
                '<URL>http://www.gnipcentral.com</URL>'.
                '<source>web</source>'.
                '<source>sms</source>'.
                '<keyword>ping</keyword>'.
                '<keyword>pong</keyword>'.
                '<place><point>45.256 -71.92</point></place>'.
                '<place>'.
                '<point>22.778 -54.998</point><elev>5280</elev><floor>3</floor><featuretypetag>City</featuretypetag><featurename>Boulder</featurename>'.
                '</place>'.
                '<place><point>77.900 - 23.998</point></place>'.
                '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
                '<actor metaURL="http://www.gnipcentral.com/users/bob">Bob</actor>'.
                '<actor uid="1444">Susan</actor>'.
                '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
                '<destinationURL>http://flickr.com</destinationURL>'.
                '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
                '<tag>cows</tag>'.
                '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
                '<to>James</to>'.
                '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
                '<regardingURL>http://blogger.com/users/posts/james</regardingURL>'.
                '</activity>';
        $d = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->sourceArray, $this->keywordArray, $this->placeArray, $this->actorArray, $this->destinationURLArray, $this->tagArray, $this->toArray, $this->regardingURLArray, null);
        $this->assertEquals($expected_xml, $d->toXML());
    }

    function testToXMLWithPayloadMediaURLUnbounds()
    {
        $expected_xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<activityID>yk994589klsl</activityID>'.
                '<URL>http://www.gnipcentral.com</URL>'.
                '<source>web</source>'.
                '<source>sms</source>'.
                '<keyword>ping</keyword>'.
                '<keyword>pong</keyword>'.
                '<place><point>45.256 -71.92</point></place>'.
                '<place>'.
                '<point>22.778 -54.998</point><elev>5280</elev><floor>3</floor><featuretypetag>City</featuretypetag><featurename>Boulder</featurename>'.
                '</place>'.
                '<place><point>77.900 - 23.998</point></place>'.
                '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
                '<actor metaURL="http://www.gnipcentral.com/users/bob">Bob</actor>'.
                '<actor uid="1444">Susan</actor>'.
                '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
                '<destinationURL>http://flickr.com</destinationURL>'.
                '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
                '<tag>cows</tag>'.
                '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
                '<to>James</to>'.
                '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
                '<regardingURL>http://blogger.com/users/posts/james</regardingURL>'.
                '<payload><title>title</title><body>body</body><mediaURL type="image" mimeType="image/png">http://www.flickr.com/tour</mediaURL><mediaURL type="movie" mimeType="video/quicktime">http://www.gnipcentral.com/login</mediaURL><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>'.
                '</activity>';
        $d = new Services_Gnip_Activity($this->at, $this->action, $this->activityID, $this->URL, $this->sourceArray, $this->keywordArray, $this->placeArray, $this->actorArray, $this->destinationURLArray, $this->tagArray, $this->toArray, $this->regardingURLArray, $this->payloadArray);
        
        $this->assertEquals($expected_xml, $d->toXML());
    }


    function testFromXml()
    {
        $xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<activityID>yk994589klsl</activityID>'.
            '<URL>http://www.gnipcentral.com</URL>'.
            '<source>web</source>'.
            '<keyword>ping</keyword>'.
            '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
            '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
            '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
            '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
            '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
            '</activity>';

        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->activityID, $a->activityID);
        $this->assertEquals($this->URL, $a->URL);
        $this->assertEquals($this->source, $a->source);
        $this->assertEquals($this->keyword, $a->keyword);
        $this->assertEquals($this->actor, $a->actor);
        $this->assertEquals($this->destinationURL, $a->destinationURL);
        $this->assertEquals($this->tag, $a->tag);
        $this->assertEquals($this->to, $a->to);
        $this->assertEquals($this->regardingURL, $a->regardingURL);
    }

    function testFromXmlNulls()
    {
        $xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '</activity>';

        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertNull($a->activityID);
        $this->assertNull($a->URL);
        $this->assertNull($a->source);
        $this->assertNull($a->keyword);
        $this->assertNull($a->actor);
        $this->assertNull($a->destinationURL);
        $this->assertNull($a->tag);
        $this->assertNull($a->to);
        $this->assertNull($a->regardingURL);    
    }

    function testFromXmlWithPlace()
    {
        $xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<place><point>45.256 -71.92</point></place>'.
            '</activity>';
        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->place, $a->place);
    }

    function testFromXmlWithPayload()
    {
        $xml ='<activity>'.
            '<at>2008-07-02T11:16:16+00:00</at>'.
            '<action>post</action>'.
            '<payload><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>'.
            '</activity>';
        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->payload, $a->payload);
    }

    function testFromXMLWithUnbounds()
    {
        $xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<source>web</source>'.
                '<source>sms</source>'.
                '<keyword>ping</keyword>'.
                '<keyword>pong</keyword>'.
                '<actor metaURL="http://www.gnipcentral.com/users/joe" uid="1222">Joe</actor>'.
                '<actor metaURL="http://www.gnipcentral.com/users/bob">Bob</actor>'.
                '<actor uid="1444">Susan</actor>'.
                '<destinationURL metaURL="http://somewhere.com/someplace">http://somewhere.com</destinationURL>'.
                '<destinationURL>http://flickr.com</destinationURL>'.
                '<tag metaURL="http://gnipcentral.com/tags/horses">horses</tag>'.
                '<tag>cows</tag>'.
                '<to metaURL="http://gnipcentral.com/users/mary">Mary</to>'.
                '<to>James</to>'.
                '<regardingURL metaURL="http://blogger.com/users/mary">http://blogger.com/users/posts/mary</regardingURL>'.
                '<regardingURL>http://blogger.com/users/posts/james</regardingURL>'.
                '</activity>';

        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->sourceArray, $a->source);
        $this->assertEquals($this->keywordArray, $a->keyword);
        $this->assertEquals($this->actorArray, $a->actor);
        $this->assertEquals($this->destinationURLArray, $a->destinationURL);
        $this->assertEquals($this->tagArray, $a->tag);
        $this->assertEquals($this->toArray, $a->to);
        $this->assertEquals($this->regardingURLArray, $a->regardingURL);
    }

    function testFromXMLWithPlaceUnbounds()
    {
        $xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<place><point>45.256 -71.92</point></place>'.
                '<place>'.
                '<point>22.778 -54.998</point><elev>5280</elev><floor>3</floor><featuretypetag>City</featuretypetag><featurename>Boulder</featurename>'.
                '</place>'.
                '<place><point>77.900 - 23.998</point></place>'.
                '</activity>';

        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->placeArray, $a->place);
    }

    function testFromXMLWithPayloadUnboundMediaURL()
    {
        $xml ='<activity>'.
                '<at>2008-07-02T11:16:16+00:00</at>'.
                '<action>post</action>'.
                '<payload><title>title</title><body>body</body><mediaURL type="image" mimeType="image/png">http://www.flickr.com/tour</mediaURL><mediaURL type="movie" mimeType="video/quicktime">http://www.gnipcentral.com/login</mediaURL><raw>H4sIAAAAAAAAAytKLAcAVduzGgMAAAA=</raw></payload>'.
                '</activity>';
        $a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));
        $this->assertEquals($this->at, $a->at->format(DATE_ATOM));
        $this->assertEquals($this->action, $a->action);
        $this->assertEquals($this->payloadArray, $a->payload);
    }


}
?>