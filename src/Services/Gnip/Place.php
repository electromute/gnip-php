<?php
class Services_Gnip_Place
{
    public $point;
    public $elev;
    public $floor;
    public $featuretypetag;
    public $featurename;
    public $relationshiptag;


    /**
     * Constructor. More information on Geo data can be found at http://georss.org/
     * 
     * @param string $point latitude and longitude separated by a space. optional
     * @param float $elev elevation optional
     * @param integer $floor building floor optional
     * @param string $featuretypetag location type (city, county, state, etc.) optional
     * @param string $featurename name of location (Boulder, Colorado, San Francisco, etc.) optional
     * @param string $relationshiptag relationship to lat/long (center, etc.) optional
     * 
     * Creates a Services_Gnip_Place object. All elements are optional.
     * 
     */
    public function __construct($point = null, $elev = null, $floor = null, $featuretypetag = null, $featurename = null, $relationshiptag = null)
    {
        $this->point = ($point != null) ? trim($point) : null;
        $this->elev = ($elev != null) ? trim($elev) : null;
        $this->floor = ($floor != null) ? trim($floor) : null;
        $this->featuretypetag = ($featuretypetag != null) ? trim($featuretypetag) : null;
        $this->featurename = ($featurename != null) ? trim($featurename) : null;
        $this->relationshiptag = ($relationshiptag != null) ? trim($relationshiptag) : null;
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
        $place = $doc->createElement('place');
        if ($this->point != null) {
            $place->appendChild($doc->createElement('point', $this->point));
        }
        if ($this->elev != null) {
            $place->appendChild($doc->createElement('elev', $this->elev));
        }
        if ($this->floor != null) {
            $place->appendChild($doc->createElement('floor', $this->floor));
        }
        if ($this->featuretypetag != null) {
            $place->appendChild($doc->createElement('featuretypetag', $this->featuretypetag));
        }
        if ($this->featurename != null){
            $place->appendChild($doc->createElement('featurename', $this->featurename));
        }
        if ($this->relationshiptag != null) {
            $place->appendChild($doc->createElement('relationshiptag', $this->relationshiptag));
        }
        if($place->hasChildNodes()){
            $root->appendChild($place);
        }

    }

    /**
     * From XML.
     * 
     * @param $xml XML data
     * @return object Services_Gnip_Place
     *
     * Converts XML formatted place to Services_Gnip_Place object.
     */
    public function fromXML($xml)
    {
       $found_point = strlen(strval($xml->point)) ? strval($xml->point) : null;
       $found_elev = strlen(strval($xml->elev)) ? strval($xml->elev) : null;
       $found_floor = strlen(strval($xml->floor)) ? strval($xml->floor) : null;
       $found_feattype = strlen(strval($xml->featuretypetag)) ? strval($xml->featuretypetag) : null;
       $found_featname = strlen(strval($xml->featurename)) ? strval($xml->featurename) : null;
       $found_relate = strlen(strval($xml->relationshiptag)) ? strval($xml->relationshiptag) : null;

       return new Services_Gnip_Place($found_point, $found_elev, $found_floor, $found_feattype, $found_featname, $found_relate);
    }

}
?>