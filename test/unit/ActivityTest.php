 <?php
require_once dirname(__FILE__).'/../test_helper.php';

class ActivityTest extends PHPUnit_Framework_TestCase
{
	function testToXml()
	{
        $xml ='<activity at="2008-07-02T11:16:16+00:00" action="upload" actor="sally" ' .
			'regarding="blog_post" source="web" tags="trains,planes,automobiles" ' .
			'to="bob" url="http://example.com"/>';

		$a = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'sally', 'blog_post', 'web', 'trains,planes,automobiles', 'bob', 'http://example.com');
		$this->assertEquals($xml, $a->toXML());

	}
    
    function testFromXml()
    {
		$xml='<activity at="2008-07-02T11:16:16+00:00" action="upload" actor="sally" ' .
			'regarding="blog_post" source="web" tags="trains,planes,automobiles" ' .
			'to="bob" url="http://example.com"/>';

		$a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));

		$expected_tags = array("trains", "planes", "automobiles");

        $this->assertEquals("2008-07-02T11:16:16+00:00", $a->at->format(DATE_ATOM));
		$this->assertEquals("upload", $a->action);
		$this->assertEquals("sally", $a->actor);
		$this->assertEquals("blog_post", $a->regarding);
		$this->assertEquals("web", $a->source);
		$this->assertEquals($expected_tags, $a->tags);
		$this->assertEquals("http://example.com", $a->url);
	}


	function testToXmlWithPayload()
	{
        $xml ='<activity at="2008-07-02T11:16:16+00:00" action="upload" actor="sally" ' .
			'regarding="blog_post" source="web" tags="trains,planes,automobiles" ' .
			'to="bob" url="http://example.com"><payload><body>body</body></payload></activity>';

		$a = new Services_Gnip_Activity('2008-07-02T11:16:16+00:00', 'upload', 'sally', 'blog_post', 'web', 'trains,planes,automobiles', 'bob', 'http://example.com',
		        new Services_Gnip_Payload('body'));
		$this->assertEquals($xml, $a->toXML());

	}

    function testFromXmlWithPayload()
    {
        $xml ='<activity at="2008-07-02T11:16:16+00:00" action="upload" actor="sally" ' .
            'regarding="blog_post" source="web" tags="trains,planes,automobiles" ' .
			'to="bob" url="http://example.com"><payload><body>body</body></payload></activity>';

		$a = Services_Gnip_Activity::fromXML(new SimpleXMLElement($xml));

		$expected_tags = array("trains", "planes", "automobiles");

        $this->assertEquals("2008-07-02T11:16:16+00:00", $a->at->format(DATE_ATOM));
		$this->assertEquals("upload", $a->action);
		$this->assertEquals("sally", $a->actor);
		$this->assertEquals("blog_post", $a->regarding);
		$this->assertEquals("web", $a->source);
		$this->assertEquals($expected_tags, $a->tags);
		$this->assertEquals("http://example.com", $a->url);
		$this->assertEquals("body", $a->payload->body);
	}
}
?>