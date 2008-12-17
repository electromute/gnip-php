<?php
class Services_Gnip_Rule
{
    var $type;
    var $value;


	/**
     * Constructor.
     * 
     * @param string $type
	 * @param string $value
     * 
     * Creates a Services_Gnip_Rule object.
     */
    public function __construct($type, $value)
    {
        $this->type = trim($type);
        $this->value = trim($value);
    }


	/**
     * To XML.
     * 
     * @return XML formatted rule data
	 *
     * Converts the rules to properly formatted XML.
     */
    public function toXML()
    {  
        $xml = new GnipSimpleXMLElement("<rule/>");
        $xml->addAttribute('type', $this->type);
        $xml->addAttribute('value', $this->value);

		return trim($xml->asXML());      
    }


	/**
     * From XML.
     * 
	 * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule
	 *
     * Converts XML formatted rule to Services_Gnip_Rule object.
     */
    public static function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Rule($xml["type"], $xml["value"]);
    }
}
?>