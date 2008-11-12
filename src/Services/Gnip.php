<?php
require_once 'Gnip/Activity.php';
require_once 'Gnip/Filter.php';
require_once 'Gnip/GnipSimpleXMLElement.php';
require_once 'Gnip/Helper.php';
require_once 'Gnip/Publisher.php';
require_once 'Gnip/Rule.php';
require_once 'Gnip/RuleType.php';
require_once 'Gnip/Payload.php';


class Services_Gnip
{
    static public $uri = "https://prod.gnipcentral.com";
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
    
    function getPublisherActivities($publisher, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        return $this->parseActivities($this->helper->doHttpGet($publisher->getUrl()."/activity/".$when.".xml"));
    }
	
	function getPublisherNotifications($publisher, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        return $this->parseActivities($this->helper->doHttpGet($publisher->getUrl()."/notification/".$when.".xml"));
    }

	function getFilterActivities($publisher, $filter, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        return $this->parseActivities($this->helper->doHttpGet($filter->getUrl($publisher)."/activity/".$when.".xml"));
    }

	function getFilterNotifications($publisher, $filter, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        return $this->parseActivities($this->helper->doHttpGet($filter->getUrl($publisher)."/notification/".$when.".xml"));
    }
    
    function createFilter($publisher, $filter)
    {
        return $this->helper->doHttpPost($filter->getCreateUrl($publisher), $filter->toXML());
    }

    function getFilter($publisher, $name)
    {
        $filter = new Services_Gnip_Filter($name);
        $xml = $this->helper->doHttpGet($filter->getUrl($publisher).".xml");
        return Services_Gnip_Filter::fromXML(new SimpleXMLElement($xml));
    }
    
    function updateFilter($publisher, $filter)
    {
        return $this->helper->doHttpPut($filter->getUrl($publisher).".xml", $filter->toXML());
    }
    
    function deleteFilter($publisher, $filter)
    {
        return $this->helper->doHttpDelete($filter->getUrl($publisher).".xml");
    }
    
    function parseActivities($xml)
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
