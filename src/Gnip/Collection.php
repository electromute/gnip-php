<?php

class Collection
{
	public $name;
	public $postURL;
	public $uids;
	
	public function __construct($name, $postURL = ''){
		$this->name = trim($name);
		$this->postURL = trim($postURL);
		$this->uids = array();
	}
	
	public function toXML(){
		$xml = new GnipSimpleXMLElement("<collection/>", LIBXML_NOXMLDECL); // NOXMLDECL only in libxml >= 2.6.21
		$xml->addAttribute('name', $this->name);
		$xml->addOptionalAttribute('postUrl', $this->postURL);
		foreach($this->uids as $uid){
			$uid_node = $xml->addChild('uid');
			$uid_node->addAttribute('name',$uid->name);
     		$uid_node->addAttribute('publisher.name', $uid->publisherName);     		
		}
		return $xml->asXML();
	}
	
	public static function fromXML(SimpleXMLElement $xml){		
		$c = new Collection($xml["name"], $xml["postUrl"]);
		foreach($xml->uid as $uid_node){
			$c->uids[] = Uid::fromXML($uid_node);
		}
		return $c;
	}
}
?>