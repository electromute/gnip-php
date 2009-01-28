<?php
class Services_Gnip_Filter
{
    public $name;
    public $fullData;
    public $postURL;
    public $rules;
    

    /**
     * Constructor.
     * 
     * @param string $name
     * @param boolean $fullData default is false
     * @param string $postURL default is empty string
     * @param array $rules array of Services_Gnip_Rule objects, default is empty
     * 
     * Creates a Services_Gnip_Filter object.
     */
    public function __construct($name, $fullData = 'false', $postURL = '', $rules = array())
    {
        $this->name = trim($name);
        $this->fullData = trim($fullData);
        $this->postURL = trim($postURL);
        $this->rules = $rules;
    }


    /**
     * Add Rules.
     * 
     * @param array $rules
     * 
     * Adds one or more rules to a Services_Gnip_Filter object.
     */
    public function addRules($rules){
        foreach ((array) $rules as $rule){
            $this->rules[] = $rule;
        }
    }


    /**
     * Remove Rules.
     * 
     * @param array $rules
     *
     * Removes one or more rules from a Services_Gnip_Filter object.
     */
    public function removeRules($rules){
        foreach ((array) $rules as $rule){
            $key = array_search($rule, $this->rules);
            unset($this->rules[$key]);
        }
    }


    /**
     * To XML.
     * 
     * @return XML formatted filter data
     *
     * Converts the filter to properly formatted XML.
     */
    public function toXML()
    {
        $xml = new GnipSimpleXMLElement("<filter/>");
        $xml->addAttribute('name', $this->name);
        $xml->addAttribute('fullData', $this->fullData);
        $xml->addOptionalChild('postURL', $this->postURL);
        foreach($this->rules as $rule){
            $rule_node = $xml->addChild('rule', $rule->value);
            $rule_node->addAttribute('type', $rule->type);
        }
        return trim($xml->asXML());
    }


    /**
     * From XML.
     * 
     * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Filter
     *
     * Converts XML formatted filter to Services_Gnip_Filter object.
     */
    public function fromXML(SimpleXMLElement $xml)
    {
        $f = new Services_Gnip_Filter($xml["name"], $xml["fullData"]);
        $f->postURL = strval($xml->postURL);
        foreach($xml->rule as $rule_node){
            $f->rules[] = Services_Gnip_Rule::fromXML($rule_node);
        }
        return $f;
    }


    /**
     * Get create filter URL.
     * 
     * @param string $publisher name of publisher
     * @return string URL
     *
     * Returns the URL to send create filter request to belonging
     * to a given publisher.
     */
    public function getCreateUrl($publisher)
    {
        return "/publishers/" . $publisher . "/filters.xml";
    }


    /**
     * Get filter URL.
     * 
     * @param string $publisher name of publisher
     * @return string URL
     *
     * Returns the URL of a given filter by name belonging to 
     * a given publisher.
     */
    public function getUrl($publisher)
    {
        return "/publishers/".$publisher."/filters/".$this->name.".xml";
    }

    /**
     * Get filter activity URL.
     * 
     * @param string $when timestamp of bucket
     * @param string $publisher name of the publisher
     * @return string URL
     *
     * Returns the URL of filter activity bucket.
     */
    public function getActivityUrl($publisher, $when){
        return "/publishers/".$publisher."/filters/".$this->name."/activity/".$when.".xml";
    }

    /**
     * Get filter notifications URL.
     * 
     * @param string $when timestamp of bucket
     * @param string $publisher name of the publisher
     * @return string URL
     *
     * Returns the URL of notification activity bucket.
     */
    public function getNotificationUrl($publisher, $when){
        return "/publishers/".$publisher. "/filters/".$this->name."/notification/".$when.".xml";
    }


    /**
     * Get index URL.
     * 
     * @param string $publisher name of publisher
     * @return string URL
     *
     * Returns the URL of filter list for a publisher you have created
     * filters on.
     */
    public function getIndexUrl($publisher)
    {
        return "/publishers/" . $publisher ."/filters.xml";
    }

}
?>