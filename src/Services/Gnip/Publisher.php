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
	    $xml = new GnipSimpleXMLElement("<publisher/>", LIBXML_NOXMLDECL); // NOXMLDECL only in libxml >= 2.6.21
		$xml->addAttribute('name', $this->name);
		return $xml->asXML();
    }
}
?>