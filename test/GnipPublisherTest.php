<?php
require_once dirname(__FILE__).'/test_helper.php';

class GnipPublisherTest extends PHPUnit_Framework_TestCase {
  
  public function testPublish() {
    $publisher = new GnipPublisher("jeremy.lightsmith@gmail.com", "test", "bob");
 
    // DATE_ISO8601 gives us 2008-07-15T15:42:47-0700
    // DATE_ATOM    gives us 2008-07-15T15:43:46-07:00 
    $atString = date_create()->format(DATE_ATOM);

    $activity = new Activity($atString,'added_friend','foo/bob1');
	$response = $publisher->publish(array($activity));
	
    $this->assertContains($response, '<result>Success</result>');
  }

}
?>