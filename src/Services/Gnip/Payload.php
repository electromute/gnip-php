<?php
class Services_Gnip_Payload
{
    public $title;
    public $body;
    public $mediaURL;
    public $mediaMetaURL;
    public $raw;


    /**
     * Constructor.
     * 
     * @param string $raw required string representation of the dataset
     * @param string $title optional
     * @param string $body optional
     * @param array  $mediaURL with optional atrributes. 2d array can be sent. optional
     * optional attributes for mediaURL are:
     * height
     * width
     * duration
     * mimeType
     * type
     * 
     * Creates a Services_Gnip_Payload object.
     * 
     */
    public function __construct($raw, $title = null, $body = null, $mediaURL = null)
    {
        $this->title = ($title != null) ? trim($title) : null;
        $this->body = ($body != null) ? trim($body) : null;
        $this->mediaURL = (is_array($mediaURL)) ? $mediaURL : null;
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
     * @param object $doc DOMDocument object
     * @param object $root DOMDocument root
     *
     * Converts the place to properly formatted XML.
     */
    public function toXML($doc, $root){
        $payload = $doc->createElement('payload');
        if ($this->title != null){
            $payload->appendChild($doc->createElement('title', $this->title));
        }
        if ($this->body != null) {
            $payload->appendChild($doc->createElement('body', $this->body));
        }
        if ($this->mediaURL != null){
            $doc->appendChildren($doc, $payload, "mediaURL", $this->mediaURL);
        }
        $payload->appendChild($doc->createElement('raw', $this->raw));
        if($payload->hasChildNodes()){
            $root->appendChild($payload);
        }
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
        $found_title = strlen(strval($xml->title)) ? strval($xml->title) : null;
        $found_body = strlen(strval($xml->body)) ? strval($xml->body) : null;
        $result = $xml->xpath('mediaURL');
        $nodesNum = count($result);
        $found_mediaURL = array();
        
        if (!empty($result)){
        if($nodesNum >= 1){
            foreach ($result as $key => $val){
                if(is_object($val)){
                    $mediaStuff['mediaURL'] = strval($val[0]);
                    foreach($val[0]->attributes() as $attrName => $attrVal) {
                        if (strlen (strval($attrVal))){
                            $mediaStuff[$attrName] = strval($attrVal);
                        }
                    }
                    $found_mediaURL[] = $mediaStuff;
                }
            }
        } else {
            $found_mediaURL = null;
        }
        } else {
            $found_mediaURL = null;
        }
        $found_raw = Services_Gnip_Payload::gzdecode(base64_decode($xml->raw));
        
        return new Services_Gnip_Payload($found_raw, $found_title, $found_body, $found_mediaURL);
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