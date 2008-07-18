<?php
class Services_Gnip_Activity
{
    public $at;  /* String, Required */
    public $uid; /* String, Required */
    public $type; /* String, Required */
    public $guid;  /* String, Optional */
    public $publisherName; /* String, Optional, "publisher.name" */

    function __construct($at, $uid, $type, $guid = '', $publisherName = '')
    {
        $this->at = is_string($at) ? new DateTime($at) : $at;
        $this->uid = trim($uid);
        $this->type = trim($type);
        $this->guid = trim($guid);
        $this->publisherName = trim($publisherName);
    }

    function setGuid($guid)
    {
        $this->guid = $guid;
    }
    
    function setPublisherName($name)
    {
        $this->publisherName = trim($name);
    }
    
    function toXML()
    { 
        $xml = new GnipSimpleXMLElement("<activity/>");
        $xml->addAttribute('at', $this->at->format(DATE_ATOM));
        $xml->addAttribute('uid', $this->uid);
        $xml->addAttribute('type', $this->type);
        $xml->addOptionalAttribute('guid', $this->guid);
        $xml->addOptionalAttribute('publisher.name', $this->publisherName);         
        return $xml->asXML();
    }
    
    static function fromXML($xml)
    {
        return new Services_Gnip_Activity(new DateTime($xml['at']), $xml['uid'], $xml['type'], $xml['guid'], $xml['publisher.name']);
    }
}
?>