<?php
class Uid
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
		$xml = new GnipSimpleXMLElement("<uid/>", LIBXML_NOXMLDECL); // NOXMLDECL only in libxml >= 2.6.21
		$xml->addAttribute('name', $this->name);
		$xml->addAttribute('publisher.name', $this->publisherName);
		return $xml->asXML();			
	}
	
	public static function fromXML(SimpleXMLElement $xml){
		return new Uid($xml["name"], $xml["publisher.name"]);
	}
}
?>