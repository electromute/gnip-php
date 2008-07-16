<?php

/**
 * This class provides convenience methods for accessing the Gnip servers and
 * performing publisher related functions. 
 */
class GnipPublisher
{
	var $helper;
    var $publisher;

    /**
	 * Initialize the class.
	 * 
	 * @type username string
	 * @param username The Gnip account username
	 * @type password string
	 * @param password The Gnip account password 
	 * @type publisher 
	 * @param publisher string The name of the publisher
	 * 
	 * Initializes a Gnip class by creating an instance of the GnipHelper 
	 * class, which provides most of the basic network and time functionality.
	 */
    function GnipPublisher($username, $password, $publisher)
    { 
        $this->helper = new GnipHelper($username, $password);
        $this->publisher = $publisher;
    }

	/**
	 * Publish activities.
	 * 
	 * @type activity array
	 * @param Array of Activity 
	 * @return string containing response from the server
	 * 
	 * This method takes in a XML document with a list of activities 
	 * sends it to the Gnip server.
	 */
    function publish($activities)
	{
        
        $url = $this->helper->GNIP_BASE_URL . "/publishers/" . $this->publisher
			. "/activity.xml";
        $xmlString = $this->buildActivitiesXml($activities);
        $xmlString = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$xmlString);
		return $this->helper->doHttpPost($url,$xmlString);
	}
	
	private function buildActivitiesXml($activities)
	{
		$activitiesXML = "";
		foreach($activities as $a)
		{
			$activitiesXML = $activitiesXML . $a->toXML();
		}
		$xml = new SimpleXMLElement(utf8_encode('<activities>' . $activitiesXML . '</activities>'));
		$dom = DomDocument::loadXML($xml->asXML());
	    $dom->schemaValidate(dirname(__FILE__) . '/Gnip/gnip.xsd'); 
		return $xml->asXML();
	}
};

?>