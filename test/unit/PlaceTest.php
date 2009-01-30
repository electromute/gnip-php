 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class PlaceTest extends PHPUnit_Framework_TestCase
{

    function testToXml()
    {
        $doc = new GnipDOMDocument();
        $root = $doc->createElement('activity');
        $doc->appendChild($root);
        
        $expected_xml = "<activity><place><point>38.2638886 -106.126131</point><elev>5.343</elev><floor>4</floor><featuretypetag>blah</featuretypetag><featurename>Chloride Mine</featurename><relationshiptag>nearby</relationshiptag></place></activity>";

        $place = new Services_Gnip_Place("38.2638886 -106.126131", 5.343, 4, "blah", "Chloride Mine", "nearby");
        $place->toXML($doc, $root);
        $this->assertEquals($expected_xml, $doc->asXML());
    }

    function testToXmlNullElements()
    {
        $doc = new GnipDOMDocument();
        $root = $doc->createElement('activity');
        $doc->appendChild($root);

        $expected_xml = "<activity/>";

        $place = new Services_Gnip_Place();
        $place->toXML($doc, $root);
        $this->assertEquals($expected_xml, $doc->asXML());
    }

    function testFromXml()
    {
        $xml = "<place><point>38.2638886 -106.126131</point><elev>5.343</elev><floor>4</floor><featuretypetag>blah</featuretypetag><featurename>Chloride Mine</featurename><relationshiptag>nearby</relationshiptag></place>";
        $place = Services_Gnip_Place::fromXML($xml);
        $this->assertEquals("38.2638886 -106.126131", $place->point);
        $this->assertEquals(5.343, $place->elev);
        $this->assertEquals(4, $place->floor);
        $this->assertEquals("blah", $place->featuretypetag);
        $this->assertEquals("Chloride Mine", $place->featurename);
        $this->assertEquals("nearby", $place->relationshiptag);    
    }
    
    function testNullStuff()
    {
        $expected_point = "38.2638886 -106.126131";
        $place = new Services_Gnip_Place($expected_point);
        $this->assertEquals($expected_point, $place->point);
        $this->assertNull($place->elev);
        $this->assertNull($place->floor);
        $this->assertNull($place->featuretypetag);
        $this->assertNull($place->featurename);
        $this->assertNull($place->relationshiptag);
    }
}
?>
