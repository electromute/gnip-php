<?php
class Services_Gnip_Rule
{
    var $type;
    var $value;

    public function __construct($type, $value)
    {
        $this->type = trim($type);
        $this->value = trim($value);
    }

    public function toXML()
    {  
        $xml = new GnipSimpleXMLElement("<rule/>");
        $xml->addAttribute('type', $this->type);
        $xml->addAttribute('value', $this->value);

		return trim($xml->asXML());      
    }

    public static function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Rule($xml["type"], $xml["value"]);
    }
}
?>