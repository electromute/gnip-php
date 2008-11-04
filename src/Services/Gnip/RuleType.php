<?php
class Services_Gnip_Rule_Type
{
    var $type;

    public function __construct($type)
    {
        $this->type = trim($type);
    }

    public function toXML()
    {
        $xml = new GnipSimpleXMLElement("<type/>");
        $xml[0] = $this->type;
		return trim($xml->asXML());
    }

    public static function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Rule_Type($xml[0]);
    }
}
?>