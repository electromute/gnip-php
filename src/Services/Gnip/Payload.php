<?php
class Services_Gnip_Payload
{
    public $body;
    public $raw;


	/**
     * Constructor.
     * 
     * @param string $type
	 * @param array $supported_rule_types
     * 
     * Creates a Services_Gnip_Payload object.
	 * 
     */
    public function __construct($body, $raw = null)
    {
        $this->body = $body;
        if($raw != null)
            $this->raw = base64_encode(gzencode($raw));
    }


	/**
     * Decode Raw.
     * 
     * @return object Services_Gnip_Payload
     * 
     * Decodes a base64 and gzipped representation of the raw data 
	 * from a publisher.
     */
    public function decodedRaw()
    {
        if($this->raw != null)
            return Services_Gnip_Payload::gzdecode(base64_decode($this->raw));
        return $this->raw;
    }


	/**
     * To XML.
     * 
     * @return XML formatted payload data
	 *
     * Converts the payload to properly formatted XML.
     */
    public function toXML()
    {
        $xml = new GnipSimpleXMLElement("<payload/>");
        $body_child = $xml->addChild("body", $this->body);
        if($this->raw != null)                
            $raw_child = $xml->addChild("raw", $this->raw);
        return trim($xml->asXML());
    }


	/**
     * From XML.
     * 
	 * @param $xml XML data
     * @return object Services_Gnip_Payload
	 *
     * Converts XML formatted payload to Services_Gnip_Payload object.
     */
    public static function fromXML($xml)
    {
        if($xml->raw != null && $xml->raw != "")
            $found_raw = Services_Gnip_Payload::gzdecode(base64_decode(strval($xml->raw)));
        else
            $found_raw = null;
        
        return new Services_Gnip_Payload(strval($xml->body), $found_raw);
    }


	/**
     * Gzip Decode.
     * 
     * @return string uncompressed data
     * 
     * Uncompresses Gzipped data and returns the resulting String.
     */
    private static function gzdecode ($data)
    {
       $flags = ord(substr($data, 3, 1));
       $headerlen = 10;
       $extralen = 0;
       $filenamelen = 0;
       if ($flags & 4) {
           $extralen = unpack('v' ,substr($data, 10, 2));
           $extralen = $extralen[1];
           $headerlen += 2 + $extralen;
       }
       if ($flags & 8) // Filename
           $headerlen = strpos($data, chr(0), $headerlen) + 1;
       if ($flags & 16) // Comment
           $headerlen = strpos($data, chr(0), $headerlen) + 1;
       if ($flags & 2) // CRC at end of file
           $headerlen += 2;
       $unpacked = gzinflate(substr($data, $headerlen));
       if ($unpacked === FALSE)
             $unpacked = $data;
       return $unpacked;
    }
}
?>