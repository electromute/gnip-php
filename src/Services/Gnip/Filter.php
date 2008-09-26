<?php
class Services_Gnip_Filter
{
    public $name;
	public $fullData;
    public $postUrl;
	public $jid;
    public $rules;
    
    public function __construct($name, $fullData = 'false', $postUrl = '', $jid = '', $rules = array())
    {
        $this->name = trim($name);
		$this->fullData = trim($fullData);
        $this->postUrl = trim($postUrl);
		$this->jid = trim($jid);
        $this->rules = $rules;
    }
    
    public function toXML()
    {
		$xml = new GnipSimpleXMLElement("<filter/>");
        $xml->addAttribute('name', $this->name);
		$xml->addAttribute('fullData', $this->fullData);
        $xml->addOptionalChild('postUrl', $this->postUrl);
		$xml->addOptionalChild('jid', $this->jid);
        foreach($this->rules as $rule){
            $rule_node = $xml->addChild('rule');
            $rule_node->addAttribute('type', $rule->type);
            $rule_node->addAttribute('value', $rule->value);             
        }
        return trim($xml->asXML());
    }
    
    public static function fromXML(SimpleXMLElement $xml)
    {
        $f = new Services_Gnip_Filter($xml["name"], $xml["fullData"]);
		$f->postUrl = strval($xml->postUrl);
		$f->jid = strval($xml->jid);
        foreach($xml->rule as $rule_node){
            $f->rules[] = Services_Gnip_Rule::fromXML($rule_node);
        }
        return $f;
    }
    
    public function getCreateUrl($publisher)
    {
        return "/publishers/" . $publisher . "/filters.xml";
    }
    
    public function getUrl($publisher)
    {
        return "/publishers/" . $publisher ."/filters/" . $this->name;
    }
}
?>