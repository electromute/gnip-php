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
    
    public $username;
    public $password;
    
    private $helper;
    
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->helper = new Services_Gnip_Helper($username, $password);
    }

    /**
     * Find a Gnip collection.
     *
     * @type name string
     * @param name The name of the collection to find
     * @return string containing response from the server
     *
     */
    function get($collection)
    {
        $url = Services_Gnip::$uri . "/collections/" . $collection->name . ".xml";

        return $this->helper->doHttpGet($url);
    }    
    public function getPublisher($name)
    {
        return null;
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
        echo $url;
        return $this->helper->doHttpPost($url,$xmlString);
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