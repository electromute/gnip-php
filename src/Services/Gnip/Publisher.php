<?php

/**
 * Represents a publisher in Gnip 
 */
class Services_Gnip_Publisher
{
    public $supported_rule_types;


	/**
     * Constructor.
     * 
     * @param string $name
	 * @param array $supported_rule_types array of Services_Gnip_Rule_Type objects
     * 
     * Creates a Services_Gnip_Publisher object. Each publisher must have at 
	 * least one rule type.
	 * The current supported rule types are:
	 * Actor 
	 * To
	 * Regarding
	 * Source
	 * Tag
	 * 
     */
    function __construct($name, $supported_rule_types = array())
    { 
        $this->name = trim($name);
        $this->supported_rule_types = $supported_rule_types;
    }


	/**
     * To XML.
     * 
     * @return XML formatted publisher data
	 *
     * Converts the publisher to properly formatted XML.
     */
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
    


	/**
     * From XML.
     * 
	 * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Publisher
	 *
     * Converts XML formatted publisher to Services_Gnip_Publisher object.
     */
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


	/**
     * Get create publisher URL.
     * 
     * @return string URL
	 *
     * Returns the URL to send create publisher request to.
     */
	public function getCreateUrl(){
		return "/publishers";
	}
    

	/**
     * Get publisher URL.
     * 
     * @return string URL
	 *
     * Returns the URL of a given publisher by name.
     */
    public function getUrl()
    {
        return "/publishers/".$this->name;
    }
    

	/**
     * Get index URL.
     * 
     * @return string URL
	 *
     * Returns the URL of publisher list.
     */
    public static function getIndexUrl()
    {
        return "/publishers.xml";
    }
}
?>