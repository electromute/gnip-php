 <?php
require_once dirname(__FILE__).'/test_helper.php';

class GnipSubscriberTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->username = "jeremy.lightsmith@gmail.com";
    $this->password = "test";
    $this->publisher = "bob";

    $this->gnipSubscriber = new GnipSubscriber($this->username, $this->password);
    $currentTime = time();
    $this->currentTimeString = gmdate(DATE_ISO8601, $currentTime);
  }

  public function testGet() {
    $gnipPublisher = new GnipPublisher($this->username, $this->password, $this->publisher);
    $currentTime = time();
    $currentTimeString = gmdate(DATE_ATOM, $currentTime);
    // Abuse the guid for this test.
    $activity = new Activity($currentTimeString, 'me','added_friend', $currentTimeString);
    $response = $gnipPublisher->publish(array($activity));
    $this->assertContains('<result>Success</result>', $response);

    $response = $this->gnipSubscriber->get($this->publisher);

    $xml = new SimpleXMLElement($response);
    $nodes = $xml->xpath('/activities/activity');
/*  http://www.php.net/simplexmlelement   #xpath
    while($node = each($activity_nodes))
    {
	    $node->at
	    $node->guid
	    $node->uid
	    $node->type
    }
*/
    $dateFromGuid = new DateTime($nodes[0]->at);
    $dateFromCurrentTimeString = new DateTime($currentTimeString);
    $this->assertEquals($dateFromCurrentTimeString, $dateFromGuid);
  }

  public function test_create_collection() {
    $collection = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection name="phpTest123">
  <uid name="me" publisher.name="{$this->publisher}"/>
  <uid name="you" publisher.name="{$this->publisher}"/>
</collection>
XML;
    $response = $this->gnipSubscriber->create_collection(utf8_encode($collection));    
    $this->assertContains("<result>Success</result>", $response);
  }

  public function test_find_collection() {
    $response = $this->gnipSubscriber->find_collection('phpTest123');
    $this->assertContains('name="phpTest123"', $response);
  }

  public function test_update_collection() {
    $collection_update = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection name="apitestcollection"><uid name="me" publisher.name="{$this->publisher}"/>
<uid name="you" publisher.name="{$this->publisher}"/>
<uid name="bob" publisher.name="{$this->publisher}"/>
</collection>
XML;

	$response = $this->gnipSubscriber->update_collection('apitestcollection', $collection_update);
	$this->assertContains('<result>Success</result>', $response);
  }


  public function test_get_activity_stream_for_collection() {
    $response = $this->gnipSubscriber->get_collection("phpTest123");
    $this->assertContains('<activities>', $response);
  }

  public function test_delete_collection() {
    $response = $this->gnipSubscriber->delete_collection("phpTest123");
    $this->assertContains('<result>Success</result>', $response);
  }
}
?>
