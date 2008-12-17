<?php
class Services_Gnip_Rule_Type
{
    var $type;

	/**
     * Constructor.
     * 
     * @param string $type
     * 
     * Creates a Services_Gnip_Rule_Type object.
     */
    public function __construct($type)
    {
        $this->type = trim($type);
    }


	/**
     * To XML.
     * 
     * @return XML formatted rule type data
	 *
     * Converts the rule types to properly formatted XML.
     */
    public function toXML()
    {
        $xml = new GnipSimpleXMLElement("<type/>");
        $xml[0] = $this->type;
		return trim($xml->asXML());
    }


	/**
     * From XML.
     * 
	 * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule_Type
	 *
     * Converts XML formatted rule type to Services_Gnip_Rule_Type object.
     */
    public static function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Rule_Type($xml[0]);
    }
}
?>