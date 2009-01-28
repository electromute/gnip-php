<?php
require_once 'Gnip/Activity.php';
require_once 'Gnip/Filter.php';
require_once 'Gnip/GnipSimpleXMLElement.php';
require_once 'Gnip/GnipDOMDocument.php';
require_once 'Gnip/Helper.php';
require_once 'Gnip/Publisher.php';
require_once 'Gnip/Rule.php';
require_once 'Gnip/RuleType.php';
require_once 'Gnip/Payload.php';
require_once 'Gnip/Place.php';


class Services_Gnip
{
    static public $uri = "https://review-v21.gnipcentral.com";
    public $helper;
    public $debug;
    public $scope;


     /**
     * Constructor.
     * 
     * @param string $username
     * @param string $password
     * @param string $publisherScope current options are 'my' and 'gnip'
     * my = publisher data you own
     * gnip = public publisher data
     * 
     * Creates a Services_Gnip object.
     */
    public function __construct($username, $password, $scope = 'my')
    {
        $this->helper = new Services_Gnip_Helper($username, $password, Services_Gnip::$uri);
        $this->scope = $this->_scopeprep($scope);
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
            return $this->helper->doHttpPost($this->scope . $publisher->getCreateUrl(), $publisher->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling createPublisher on $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
            return $this->helper->doHttpPut($this->scope . $publisher->getUrl(), $publisher->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling updatePublisher on $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
    function getPublisher($name) {
        $publisher = new Services_Gnip_Publisher($name);
        try {
            $xml = $this->helper->doHttpGet($this->scope . $publisher->getUrl());
            return $publisher->fromXML(new SimpleXMLElement($xml));
        } catch (Exception $e) {
            $message = "There was a problem when calling getPublisher on $name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Get all Publishers.
     * 
     * @param string scope optional scope to override Services_Gnip 
     * object scope
     *
     * @return array containing Services_Gnip_Publisher objects
     * 
     * Retrieves all publishers from the Gnip servers. 
     */
    function getPublishers($scope='') {
        if(strlen($scope)) {
            try {
                $scope = $this->_scopeprep($scope);
            } catch (Exception $e){
                $message = "There was a problem when calling getPublishers(). Status message: ";
                $this->_handleDebug($message, $this->debug, $e);
            }
        } else {
            $scope = $this->scope;  
        } 
        try {
            $xml = $this->helper->doHttpGet($scope . Services_Gnip_Publisher::getIndexUrl());
            $xml = new SimpleXMLElement($xml);
            $publishers = array();
            foreach($xml->children() as $child) {
                $publishers[] = Services_Gnip_Publisher::fromXML($child);
            }
            return $publishers;
        } catch (Exception $e){
            $message = "There was a problem when calling getPublishers(). Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
    function publish($publisher, $activitiesArray) {        
        $url =  Services_Gnip::$uri . $this->scope . $publisher->getPublishToUrl();
        $xmlString = $this->_buildChildXml($activitiesArray, 'activities');
        $xmlString = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$xmlString);
        try {
            return $this->helper->doHttpPost($this->scope . $publisher->getPublishToUrl(), $xmlString);
        } catch (Exception $e){
            $message = "There was a problem when calling publish on publisher $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
    function getPublisherActivities($publisher, $when = "current") {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            $activities = $this->parseActivities($this->helper->doHttpGet($this->scope . $publisher->getActivityUrl($when)));
            return $activities;
        } catch (Exception $e){
            $message = "There was a problem when calling getPublisherActivities on publisher $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
    function getPublisherNotifications($publisher, $when = "current") {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($this->scope . $publisher->getNotificationUrl($when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getPublisherNotifications on publisher $publisher->name. Status message: ";
           $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Get Filter Activities.
     * 
     * @param string $publisher name of publisher
     * @param object $filter Services_Gnip_Filter
     * @param long $when optional bucket time, defaults to current
     * @return array of filtered activity objects
     * 
     * Retrieves filtered activitiy data by publisher from the Gnip servers
     * given a valid filter.
     * An optional time parameter can be passed, defaults to current.
     */
    function getFilterActivities($publisher, $filter, $when = "current") {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($this->scope . $filter->getActivityUrl($publisher, $when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getFilterActivities on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }



     /**
     * Get Filter Notifications.
     * 
     * @param string $publisher name of publisher
     * @param object $filter Services_Gnip_Filter
     * @param long $when optional bucket time, defaults to current
     * @return array of filtered notification objects
     * 
     * Retrieves filtered notification data by publisher from the Gnip servers
     * given a valid filter.
     * An optional time parameter can be passed, defaults to current.
     */
    function getFilterNotifications($publisher, $filter, $when = "current") {
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($this->scope . $filter->getNotificationUrl($publisher, $when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getFilterNotifications on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        } 
    }



     /**
     * Create Filter.
     * 
     * @param string $publisher publisher name
     * @param object $filter Services_Gnip_Filter
     * @return string response from the server
     * 
     * Creates a filter on the Gnip servers for any publisher in the system.
     */    
    function createFilter($publisher, $filter)
    {
        try{
            return $this->helper->doHttpPost($this->scope . $filter->getCreateUrl($publisher), $filter->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling createFilter on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
            $xml = $this->helper->doHttpGet($this->scope . $filter->getUrl($publisher));
            return Services_Gnip_Filter::fromXML(new SimpleXMLElement($xml));
        } catch (Exception $e){
            $message = "There was a problem when calling getFilter on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
            return $this->helper->doHttpPut($this->scope . $filter->getUrl($publisher), $filter->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling updateFilter on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
            return $this->helper->doHttpDelete($this->scope . $filter->getUrl($publisher));
        } catch (Exception $e){
            $message = "There was a problem when calling deleteFilter on publisher $publisher. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }

     /**
     * Rule Exists.
     * 
     * @param string $publisher name of the publisher that contains the filter
     * @param string $filter name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @return boolean true or false
     * 
     * Checks to see if a rule exists for a given filter/publisher combination.
     */
    function ruleExists($publisher, $filter, $rule)
    {
        try {
            $status = $this->helper->doHttpGet($this->scope . $rule->getUrl($publisher, $filter)."?type=$rule->type&value=$rule->value");
        } catch (Exception $e){
            $message = "There was a problem when calling getRule on publisher $publisher and filter $filter. Status message: ";
            error_log($message . $e->getMessage(), 0);
            return 0;
        }
        return 1;
    }

     /**
     * Delete Rule.
     * 
     * @param string $publisher name of the publisher that contains the filter
     * @param string $filter name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @return string response from the server
     * 
     * Deletes a rule given an existing valid publisher/filter combination.
     */
    function deleteRule($publisher, $filter, $rule)
    {
        try {
            return $this->helper->doHttpDelete($this->scope . $rule->getUrl($publisher, $filter)."?type=$rule->type&value=$rule->value");
        } catch (Exception $e){
            $message = "There was a problem when calling deleteRule on publisher $publisher with filter $filter on rule type $rule->type and value $rule->value. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Get Rule.
     * 
     * @param string $publisher name of the publisher that contains the filter
     * @param string $filter name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @return Service_Gnip_Rule object
     * 
     * Gets a rule given an existing valid publisher/filter combination. Used
     * mostly for verification that the rule does exist.
     */
    function getRule($publisher, $filter, $rule)
    {
        try {
            $xml = $this->helper->doHttpGet($this->scope . $rule->getUrl($publisher, $filter)."?type=$rule->type&value=$rule->value");
            return Services_Gnip_Rule::fromXML(new SimpleXMLElement($xml));
        } catch (Exception $e){
            $message = "There was a problem when calling getRule on publisher $publisher and filter $filter with rule type $rule->type and value $rule->value. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }

    /**
     * Add a Batch of Rules.
     * 
     * @param string $publisher name of the publisher that contains the filter
     * @param string $filter name of filter that contains rules
     * @param array $rulesArray array of Service_Gnip_Rule objects
     * @return string response from the server
     * 
     * Gets a rule given an existing valid publisher/filter combination.
     */
    function addBatchRules($publisher, $filter, $rulesArray){
        if(is_array($rulesArray)){
            $rules = $this->_buildChildXml($rulesArray, "rules");
            $rules = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$rules);
            $url = $rulesArray[0]->getUrl($publisher, $filter);
        } else {
            $rules = $rulesArray->toXML();
            $url = $rulesArray->getUrl($publisher, $filter);	
        }
        try {
            return $this->helper->doHttpPost($this->scope . $url, $rules);
        } catch (Exception $e){
            $message = "There was a problem when calling addBatchRules on publisher $publisher with filter $filter. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
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
     * Handle Debugging Messages.
     * 
     * @param string $message message sent by the function
     * @param boolean $debug debug setting for this object
     * @param string $e exception caught
     * 
     * Configuration setting to turn debugging on or off. If true, the debug 
     * messages will display in the browser. Bugs will still be written to the PHP 
     * Logs regardless of setting.
     */
    private function _handleDebug($message, $debug, $e){
        if ($debug){
            echo $message . $e->getMessage();
        }
        error_log($message . $e->getMessage(), 0);
    }


     /**
     * Build XML nodes for batch types of things.
     * 
     * @param object $batchArray array of objects
     * @param string $name name of object for xml formatting
     * @return string XML formatted batch data
     * 
     * Private function to format data into XML.
     */
    private function _buildChildXml($batchArray, $name)
    {
        $batchXML = "";
        foreach($batchArray as $item)
        {
            $batchXML .= $item->toXML();
        }
        $xml = new SimpleXMLElement(utf8_encode('<'.$name.'>' . $batchXML . '</'.$name.'>'));
        $doc = new DOMDocument();
        $doc->loadXML($xml->asXML());
        $doc->schemaValidate(dirname(__FILE__) . '/Gnip/gnip.xsd');
        return $xml->asXML();
    }
    
    
    /**
     * Makes sure the scope is allowed and correctly formatted.
     * Current choices are 'gnip' or 'my'.
     * 
     * @param string $scope name of scope being checked
     * @return string $scope with proper formatting
     * 
     * Private function to format scope string.
     */
    private function _scopeprep($scp){
        return "/" . trim($scp);
    }
}
?>
