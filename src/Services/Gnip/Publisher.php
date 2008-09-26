<?php

/**
 * Represents a publisher in Gnip 
 */
class Services_Gnip_Publisher
{
    function __construct($name)
    { 
        $this->name = trim($name);
    }

    function toXML()
    {
        $xml = new GnipSimpleXMLElement("<publisher/>");
        $xml->addAttribute('name', $this->name);
        return trim($xml->asXML());
    }
    
    function fromXML($xml) 
    {
        if ($xml->getName() != 'publisher') { throw new Exception("expected publisher"); }
        
        return new Services_Gnip_Publisher($xml["name"]);
    }
    
    public function getUrl()
    {
        return "/publishers/".$this->name;
    }
    
    public static function getIndexUrl()
    {
        return "/publishers.xml";
    }
}
?>