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
    public $debug;
    
	
	/**
     * Constructor.
     * 
     * @param string $username
	 * @param String $password
     * 
     * Creates a Services_Gnip object.
     */
    public function __construct($username, $password)
    {
        $this->helper = new Services_Gnip_Helper($username, $password, Services_Gnip::$uri);
    }


	/**
     * Create Publisher.
     * 
     * @param object $publisher Services_Gnip_Publisher
     * @return string response from the server
     * 
     * Creates a publisher on the Gnip server.
     */
	function createPublisher($publisher){
		try {
			return $this->helper->doHttpPost($publisher->getCreateUrl($publisher), $publisher->toXML());
		} catch (Exception $e){
			$message = "There was a problem when calling createPublisher on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
	}
	
	
	/**
     * Update Publisher.
     * 
     * @param object $publisher Services_Gnip_Publisher 
     * @return string response from the server
     * 
     * Updates an existing publisher on the Gnip server. You must be the publisher
	 * owner to update a publisher. 
     */
	function updatePublisher($publisher){
		try {
			return $this->helper->doHttpPut($publisher->getUrl($publisher), $publisher->toXML());
		} catch (Exception $e){
			$message = "There was a problem when calling updatePublisher on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
	}
    

	/**
     * Get a Publisher.
     * 
     * @param string $name name of an existing publisher
     * @return array containing publisher object
     * 
     * Retrieves a single publisher by name. 
     */
    function getPublisher($name)
    {
		$publisher = new Services_Gnip_Publisher($name);
    	try {
        $xml = $this->helper->doHttpGet($publisher->getUrl().".xml");
        return $publisher->fromXML(new SimpleXMLElement($xml));
    	} catch (Exception $e) {
			$message = "There was a problem when calling getPublisher on $name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
    	}
    }
    

	/**
     * Get all Publishers.
     * 
     * @return array containing Services_Gnip_Publisher objects
     * 
     * Retrieves all publishers from the Gnip servers. 
     */
    function getPublishers()
    {
    	try {
        	$xml = $this->helper->doHttpGet(Services_Gnip_Publisher::getIndexUrl());
        	$xml = new SimpleXMLElement($xml);
        	$publishers = array();
        	foreach($xml->children() as $child) {
            	$publishers[] = Services_Gnip_Publisher::fromXML($child);
       		}
        	return $publishers;
    	} catch (Exception $e){
			$message = "There was a problem when calling getPublishers(). Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
    	}
    }

    
    /**
     * Publish.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param array $activities array of Services_Gnip_Activity objects
     * @return string response from the server
     * 
     * Publishes data to the Gnip server. You must be the publisher
     * owner to publish data under a publisher. 
     */
    function publish($publisher, $activities)
    {        
        $url =  Services_Gnip::$uri . "/publishers/" . $publisher->name . "/activity.xml";
        $xmlString = $this->_buildActivitiesXml($activities);
        $xmlString = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$xmlString);
		try {
			return $this->helper->doHttpPost($publisher->getUrl()."/activity.xml", $xmlString);
		} catch (Exception $e){
			$message = "There was a problem when calling publish on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }


	/**
     * Get Publisher Activities.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param long $when optional bucket time, defaults to current
     * @return array containing activity objects
     * 
     * Retrieves activity data of a given publisher from the Gnip servers. 
	 * An optional time parameter can be passed, defaults to current.
     */    
    function getPublisherActivities($publisher, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
		try {
        	$activities = $this->parseActivities($this->helper->doHttpGet($publisher->getUrl()."/activity/".$when.".xml"));
			return $activities;
		} catch (Exception $e){
			$message = "There was a problem when calling getPublisherActivities on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }


	/**
     * Get Publisher Notifications.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param long $when optional bucket time, defaults to current
     * @return array containing notification objects
     * 
     * Retrieves notification data of a given publisher from the Gnip servers. 
	 * An optional time parameter can be passed, defaults to current.
     */	
	function getPublisherNotifications($publisher, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
		try {
			return $this->parseActivities($this->helper->doHttpGet($publisher->getUrl()."/notification/".$when.".xml"));
		} catch (Exception $e){
			$message = "There was a problem when calling getPublisherNotifications on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
        
    }


	/**
     * Get Filter Activities.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param object $filter Services_Gnip_Filter
	 * @param long $when optional bucket time, defaults to current
     * @return array of filtered activity objects
     * 
     * Retrieves filtered activitiy data by publisher from the Gnip servers
	 * given a valid filter.
 	 * An optional time parameter can be passed, defaults to current.
     */
	function getFilterActivities($publisher, $filter, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
		try {
			return $this->parseActivities($this->helper->doHttpGet($filter->getUrl($publisher)."/activity/".$when.".xml"));
		} catch (Exception $e){
			$message = "There was a problem when calling getFilterActivities on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }



	/**
     * Get Filter Notifications.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param object $filter Services_Gnip_Filter
	 * @param long $when optional bucket time, defaults to current
     * @return array of filtered notification objects
     * 
     * Retrieves filtered notification data by publisher from the Gnip servers
	 * given a valid filter.
 	 * An optional time parameter can be passed, defaults to current.
     */
	function getFilterNotifications($publisher, $filter, $when = "current") 
    {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
		try {
			return $this->parseActivities($this->helper->doHttpGet($filter->getUrl($publisher)."/notification/".$when.".xml"));
		} catch (Exception $e){
			$message = "There was a problem when calling getFilterNotifications on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		} 
    }



	/**
     * Create Filter.
     * 
	 * @param object $publisher Services_Gnip_Publisher
	 * @param object $filter Services_Gnip_Filter
     * @return string response from the server
     * 
     * Creates a filter on the Gnip servers for any publisher in the system.
     */    
    function createFilter($publisher, $filter)
    {
		try{
			return $this->helper->doHttpPost($filter->getCreateUrl($publisher), $filter->toXML());
		} catch (Exception $e){
			$message = "There was a problem when calling createFilter on $publisher->name. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }


	/**
     * Get Filter.
     * 
	 * @param string $publisher name of the publisher that contains the filter
	 * @param string $name name of filter
     * @return array of filter objects
     * 
     * Retrieves a given filter from a given publisher. You must be the filter
	 * owner to retrieve the filter.
     */
    function getFilter($publisher, $name)
    {
        $filter = new Services_Gnip_Filter($name);
		try {
			$xml = $this->helper->doHttpGet($filter->getUrl($publisher).".xml");
	        return Services_Gnip_Filter::fromXML(new SimpleXMLElement($xml));
		} catch (Exception $e){
			$message = "There was a problem when calling getFilter on $publisher. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }
    

	/**
     * Update Filter.
     * 
	 * @param string $publisher name of the publisher that contains the filter
	 * @param object $filter Services_Gnip_Filter
     * @return string response from the server
     * 
     * Updates the filter properties on a given filter. You must be the 
	 * filter owner to update a filter.
     */
    function updateFilter($publisher, $filter)
    {
		try {
			return $this->helper->doHttpPut($filter->getUrl($publisher).".xml", $filter->toXML());
		} catch (Exception $e){
			$message = "There was a problem when calling updateFilter on $publisher. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }
    

	/**
     * Delete Filter.
     * 
	 * @param string $publisher name of the publisher that contains the filter
	 * @param object $filter Services_Gnip_Filter
     * @return string response from the server
     * 
     * Deletes a given filter from a given publisher.
     */
    function deleteFilter($publisher, $filter)
    {
		try {
			return $this->helper->doHttpDelete($filter->getUrl($publisher).".xml");
		} catch (Exception $e){
			$message = "There was a problem when calling deleteFilter on $publisher. Status message: ";
			if ($this->debug){
				echo $message . $e->getMessage();
			}
    		error_log($message . $e->getMessage(), 0);
		}
    }
    

	/**
     * Parse Activities.
     * 
	 * @param XML $xml
     * @return array of objects from the request
     * 
     * Parses XML data from the server into an array of objects.
     */
    function parseActivities($xml)
    {
        $xml = new SimpleXMLElement($xml);
        $activities = array();
        foreach($xml->children() as $child) {
            $activities[] = Services_Gnip_Activity::fromXML($child);
        }
        return $activities;
    }


	/**
     * Set Debugging.
     * 
	 * @param boolean $debug
     * 
     * Configuration setting to turn debugging on or off. If true, the debug 
	 * messages will display in the browser. Bugs will still be written to the PHP 
	 * Logs regardless of setting.
     */
    function setDebugging($debug){
		$this->debug = $debug;
	}



	/**
     * Build XML for activities.
     * 
	 * @param object $activities Services_Gnip_Activity
	 * @return string XML formatted activity data
     * 
     * Private function to format activity data into XML.
     */
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
