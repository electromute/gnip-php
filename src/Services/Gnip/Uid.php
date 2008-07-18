<?php
class Services_Gnip_Uid
{
    var $name; /* required */
    var $publisherName; /* optional*/

    public function __construct($name, $publisherName)
    {
        $this->name = trim($name);
        $this->publisherName = trim($publisherName);
    }

    public function toXML()
    {  
        $xml = new GnipSimpleXMLElement("<uid/>");
        $xml->addAttribute('name', $this->name);
        $xml->addAttribute('publisher.name', $this->publisherName);
        return $xml->asXML();      
    }

    public static function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Uid($xml["name"], $xml["publisher.name"]);
    }
}
?>