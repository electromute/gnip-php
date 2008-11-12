<?php
class Services_Gnip_Payload
{
    public $body;
    public $raw;

    public function __construct($body, $raw = null)
    {
        $this->body = $body;
        if($raw != null)
            $this->raw = base64_encode(gzcompress($raw));
    }

    public function decodedRaw()
    {
        if($this->raw != null)
            return gzuncompress(base64_decode($this->raw));
        return $this->raw;
    }

    public function toXML()
    {
        $xml = new GnipSimpleXMLElement("<payload/>");
        $body_child = $xml->addChild("body", $this->body);
        if($this->raw != null)                
            $raw_child = $xml->addChild("raw", $this->raw);
        return trim($xml->asXML());
    }

    public static function fromXML($xml)
    {
        if($xml->raw != null && $xml->raw != "")
            $found_raw = gzuncompress(base64_decode(strval($xml->raw)));
        else
            $found_raw = null;
        
        return new Services_Gnip_Payload(strval($xml->body), $found_raw);
    }
}
?>