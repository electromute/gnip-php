<?php

/**
 * Represents a publisher in Gnip 
 */
class Services_Gnip_Publisher
{
    public $supported_rule_types;

    function __construct($name, $supported_rule_types = array())
    { 
        $this->name = trim($name);
        $this->supported_rule_types = $supported_rule_types;
    }

    function toXML()
    {
        $xml = new GnipSimpleXMLElement("<publisher/>");
        $xml->addAttribute('name', $this->name);        
        $child = $xml->addChild("supportedRuleTypes");
        foreach($this->supported_rule_types as $rule_type){
            $child->addChild('type', $rule_type->type);
        }
        return trim($xml->asXML());
    }
    
    function fromXML($xml) 
    {
        if ($xml->getName() != 'publisher') { throw new Exception("expected publisher"); }
        $publisher = new Services_Gnip_Publisher($xml["name"], array());
        $supportedRuleTypes = $xml->supportedRuleTypes;
        if($supportedRuleTypes) {
            foreach($supportedRuleTypes->children() as $rule_type){
                $publisher->supported_rule_types[] = Services_Gnip_Rule_Type::fromXML($rule_type);
            }
        }
        return $publisher;
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