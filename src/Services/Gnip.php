<?php
require_once 'Gnip/Activity.php';
require_once 'Gnip/Collection.php';
require_once 'Gnip/GnipSimpleXMLElement.php';
require_once 'Gnip/Helper.php';
require_once 'Gnip/Publisher.php';
require_once 'Gnip/Uid.php';


class Services_Gnip
{
    static public $uri = "https://s.gnipcentral.com";
    public $helper;
    
    public function __construct($username, $password)
    {
        $this->helper = new Services_Gnip_Helper($username, $password, Services_Gnip::$uri);
    }
    
    function getPublisher($name)
    {
        $publisher = new Services_Gnip_Publisher($name);
        $xml = $this->helper->doHttpGet($publisher->getUrl().".xml");
        return Services_Gnip_Publisher::fromXML(new SimpleXMLElement($xml));
    }
    
    function getPublishers()
    {
        $xml = $this->helper->doHttpGet(Services_Gnip_Publisher::getIndexUrl());

        $xml = new SimpleXMLElement($xml);
        $publishers = array();
        foreach($xml->children() as $child) {
            $publishers[] = Services_Gnip_Publisher::fromXML($child);
        }
        return $publishers;
    }
    
    /**
     * Publish activities.
     * 
     * @type activity array
     * @param Array of Services_Gnip_Activity 
     * @return string containing response from the server
     * 
     * This method takes in a XML document with a list of activities 
     * sends it to the Gnip server.
     */
    function publish($publisher, $activities)
    {        
        $url =  Services_Gnip::$uri . "/publishers/" . $publisher->name . "/activity.xml";
        $xmlString = $this->_buildActivitiesXml($activities);
        $xmlString = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$xmlString);
        return $this->helper->doHttpPost($publisher->getUrl()."/activity.xml", $xmlString);
    }
    
    function getActivities($resource, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        return $this->parseActivities($this->helper->doHttpGet($resource->getUrl()."/activity/".$when.".xml"));
    }
    
    function createCollection($collection)
    {
        $this->helper->doHttpPost($collection->getCreateUrl(), $collection->toXML());
    }

    /**
     * Find a Gnip collection.
     *
     * @type name string
     * @param name The name of the collection to find
     * @return string containing response from the server
     *
     */
    function getCollection($name)
    {
        $collection = new Services_Gnip_Collection($name);
        $xml = $this->helper->doHttpGet($collection->getUrl().".xml");
        return Services_Gnip_Collection::fromXML(new SimpleXMLElement($xml));
    }
    
    function updateCollection($collection)
    {
        $this->helper->doHttpPut($collection->getUrl().".xml", $collection->toXML());
    }
    
    function deleteCollection($collection)
    {
        $this->helper->doHttpDelete($collection->getUrl().".xml");
    }
    
    private function parseActivities($xml)
    {
        $xml = new SimpleXMLElement($xml);
        $activities = array();
        foreach($xml->children() as $child) {
            $activities[] = Services_Gnip_Activity::fromXML($child);
        }
        return $activities;
    }
    
    private function _buildActivitiesXml($activities)
    {
        $activitiesXML = "";
        foreach($activities as $a)
        {
            $activitiesXML = $activitiesXML . $a->toXML();
        }
        $xml = new SimpleXMLElement(utf8_encode('<activities>' . $activitiesXML . '</activities>'));
        $doc = new DOMDocument();
        $doc->loadXML($xml->asXML());
        $doc->schemaValidate(dirname(__FILE__) . '/Gnip/gnip.xsd'); 
        return $xml->asXML();
    }
}
?>