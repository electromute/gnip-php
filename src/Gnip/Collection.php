<?php

class Collection
{
	public $name;
	public $postURL;
	
	public function __construct($name, $postURL = '')
	{
		$this->name = trim($name);
		$this->postURL = trim($postURL);
	}
	public function toXML()
	{
		$xml = new GnipSimpleXMLElement("<collection/>", LIBXML_NOXMLDECL); // NOXMLDECL only in libxml >= 2.6.21
		$xml->addAttribute('name', $this->name);
		$xml->addOptionalAttribute('postUrl', $this->postURL);			
		return $xml->asXML();
	}
}
?>