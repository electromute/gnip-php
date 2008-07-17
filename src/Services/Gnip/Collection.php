<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is the short description of Collection
 *
 * This is a longer description of Collection that has many more details.
 *
 * PHP version 5.1.0
 *
 * LICENSE: TODO
 *
 * @category   Services
 * @package    Services_Gnip
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  2008 Gnip
 * @license    TODO
 * @version    CVS: $Id:$
 * @link       http://example.com/gnip-php
 */
class Services_Gnip_Collection
{
    public $name;
    public $postURL;
    public $uids;
    
    public function __construct($name, $postURL = '')
    {
        $this->name = trim($name);
        $this->postURL = trim($postURL);
        $this->uids = array();
    }
    
    public function toXML()
    {
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
    
    public static function fromXML(SimpleXMLElement $xml)
    {
        $c = new Services_Gnip_Collection($xml["name"], $xml["postUrl"]);
        foreach($xml->uid as $uid_node){
            $c->uids[] = Services_Gnip_Uid::fromXML($uid_node);
        }
        return $c;
    }
    
    public function getCreateUrl()
    {
        return "/collections.xml";
    }
    
    public function getUrl()
    {
        return "/collections/".$this->name.".xml";
    }
}
?>