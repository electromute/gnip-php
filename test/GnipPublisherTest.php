<?php
require_once 'PHPUnit/Framework.php';

function __autoload($class_name) {
    $dir = dirname(__FILE__);
    $path = $dir.'/../src/' . $class_name . '.php';
    require_once $path;
}

class GnipPublisherTest extends PHPUnit_Framework_TestCase {
  
  public function testPublish() {
    $gnipPublisher = new GnipPublisher("", "", "");
    $currentTime = time();
    $currentTimeString = gmdate(DATE_ISO8601, $currentTime);

    $activity = '<?xml version="1.0" encoding="UTF-8"?>'.'<activities>'. '<activity at="' . $currentTimeString . '" type="added_friend" uid="me"/>' . '</activities>';
    $response = $gnipPublisher->publish($activity);
    $this->assertContains($response, '<result>Success</result>');
  }
}
?>