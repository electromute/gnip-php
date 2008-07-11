<?php
require 'XML_serializser'
function __autoload($class_name) {
    $dir = dirname(__FILE__);
    $path = $dir.'/../src/' . $class_name . '.php';
    require_once $path;
}

/**
   * Returns a constructed XML string suitable for sending to GnipSubscriber::update_collection.
   * @param $publisher      gnip publisher name for the ids (e.g. "twitter")
   * @param $collectionName name of this gnip collection
   * @param $ids            array of uid values for usernames for the publisher
   */
  function makeCollectionXml($publisher, $collectionName, $postUrl, $ids) {
    $data = array();
    $data['name'] = $collectionName;
    //$data['postUrl'] = $postUrl; // TODO: turn this on once we're not behind the firewall (gnip can't hit it, so it fails)
    $uids = array();
    foreach ($ids as $id) {
      $uids[] = array('name' => $id, 'publisher.name' => $publisher);
    }
    $data['uid'] = $uids;

    $options = array(
      'addDecl' => true,
      'encoding' => 'UTF-8',
      'defaultTagName' => 'elem',
      'rootName' => 'collection',
      'mode' => 'simplexml',
      'scalarAsAttributes' => true,
      'indent' => '',
      'linebreak' => "\n",
    );
    $s = new XML_Serializer($options);
    $s->serialize($data);
    $xml = $s->getSerializedData();

    return $xml;
    }

    $collectionFromFunction = makeCollectionXml("test","testCollection","blah", array("oneid","twoids"));
    $username = "system@gnipcentral.com";
    $password = "sys3tem";

    $gnipSubscriber = new GnipSubscriber($username, $password);

    $collection = "<?xml version='1.0' encoding='UTF-8'?>\n" .
                        "    <collection name='sdev-digg'>\n " .
       "     <uid name='gtrevg' publisher.name='digg' />\n " .
           "     <uid name='jsmarr' publisher.name='digg' />\n " .
                                               "    </collection>";

    $otherCollection = '<?xml version="1.0" encoding="UTF-8"?><collection name="sdev-digg"><uid name="gtrevg" publisher.name="digg" /><uid name="Garret" publisher.name="digg" /><uid name="Magistrix" publisher.name="digg" /><uid name="petelester" publisher.name="digg" /><uid name="QWin15" publisher.name="digg" /><uid name="singleton" publisher.name="digg" /><uid name="jsmarr" publisher.name="digg" /></collection>';

    echo "Collection to create is " . $collectionFromFunction . "\n";
    $response = $gnipSubscriber->create_collection($collectionFromFunction);
    echo "Response is" . $response . "\n";

?>

