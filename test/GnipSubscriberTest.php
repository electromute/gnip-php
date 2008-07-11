<?php
require_once 'PHPUnit/Framework.php';

function __autoload($class_name) {
    $dir = dirname(__FILE__);
    $path = $dir.'/../src/' . $class_name . '.php';
    require_once $path;
}

class GnipSubscriberTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->username = "system@gnipcentral.com";
    $this->password = "sys3tem";
    $this->publisher = "nytimes";

    $this->gnipSubscriber = new GnipSubscriber($this->username, $this->password);
    $currentTime = time();
    $this->currentTimeString = gmdate(DATE_ISO8601, $currentTime);
  }

  public function testGet() {
    $gnipPublisher = new GnipPublisher($this->username, $this->password, $this->publisher);
    $currentTime = time();
    $currentTimeString = gmdate(DATE_ISO8601, $currentTime);

    $activity = '<?xml version="1.0" encoding="UTF-8"?>'.'<activities>'. '<activity at="' . $currentTimeString . '" type="added_friend" uid="me"/>' . '</activities>';
    $response = $gnipPublisher->publish($activity);
    $this->assertContains('<result>Success</result>', $response);

    $response = $this->gnipSubscriber->get($this->publisher);
    $this->assertContains($this->currentTimeString, $response);
  }

  public function test_create_collection() {
    $collection = '<?xml version="1.0" encoding="UTF-16"?>\n' .
        '<collection name="phpTest123">\n' .
          '<uid name="me" publisher.name="'. $this->publisher . '"/>'.
          '<uid name="you" publisher.name="'. $this->publisher . '"/>'.
        '</collection>';


    $response = $this->gnipSubscriber->create_collection($collection);
    echo 'response is ' . $response;
    $this->assertContains('<result>Success</result>', $response);
  }

  public function test_find_collection() {
    $response = $this->gnipSubscriber->find_collection('phpTest123');
    $this->assertContains('name="phpTest123"', $response);
  }

  public function test_update_collection() {
    $collection_update = '<?xml version="1.0" encoding="UTF-16"?>\n' .
        '<collection name="phpTest123">\n' .
          '<uid name="me" publisher.name="'. $this->publisher . '"/>'.
          '<uid name="you" publisher.name="'. $this->publisher . '"/>'.
          '<uid name="bob" publisher.name="'. $this->publisher . '"/>'.
        '</collection>';
    $response = $this->gnipSubscriber->update_collection('phpTest123', $collection_update);
    echo 'response is ' . $response;
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
