<?php
require_once dirname(__FILE__).'/test_helper.php';

class GnipPublisherTest extends PHPUnit_Framework_TestCase {
  
  public function testPublish() {
    $gnipPublisher = new GnipPublisher("jeremy.lightsmith@gmail.com", "test", "bob");
    $currentTime = time();
    $currentTimeString = gmdate(DATE_ISO8601, $currentTime);

    $activity = '<?xml version="1.0" encoding="UTF-8"?>'.'<activities>'. '<activity guid="foo/bob0" at="' . $currentTimeString . '" type="added_friend" />' . '</activities>';
    $response = $gnipPublisher->publish($activity);
    $this->assertContains($response, '<result>Success</result>');
  }
}
?>