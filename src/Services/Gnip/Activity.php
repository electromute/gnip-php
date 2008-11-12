<?php
class Services_Gnip_Activity
{
    public $at;
    public $action;
    public $actor; 
    public $regarding;
    public $source;
	public $tags;
	public $to;
	public $url;
	public $payload;

    function __construct($at, $action, $actor, $regarding, $source, $tags, $to, $url, $payload = null)
    {
        $this->at = is_string($at) ? new DateTime($at) : $at;
        $this->action = trim($action);
        $this->actor = trim($actor);
        $this->regarding = trim($regarding);
        $this->source = trim($source);
		$this->tags = is_string($tags) ? split(',', $tags) : $tags;
		$this->to = trim($to);
		$this->url = trim($url);
		$this->payload = $payload;
    }
    
    function toXML()
    { 
        $xml = new GnipSimpleXMLElement("<activity/>");
        $xml->addAttribute('at', $this->at->format(DATE_ATOM));
        $xml->addAttribute('action', $this->action);
        $xml->addAttribute('actor', $this->actor);
        $xml->addOptionalAttribute('regarding', $this->regarding);
		$xml->addOptionalAttribute('source', $this->source);   
		$xml->addOptionalAttribute('tags', implode(",", $this->tags));  
		$xml->addOptionalAttribute('to', $this->to);
		$xml->addOptionalAttribute('url', $this->url);

		if($this->payload != null)
		{
		    $payloadChild = $xml->addChild('payload');
		    $payloadChild->addChild('body', $this->payload->body);
		    if($this->payload->raw != null)
		        $payloadChild->addChild('raw', $this->payload->raw);
		}
        return trim($xml->asXML());
    }
    
    static function fromXML($xml)
    {

        if($xml->payload != null && $xml->payload->body != "")        
            $found_payload = Services_Gnip_Payload::fromXML($xml->payload);
        else
            $found_payload = null;

        return new Services_Gnip_Activity(new DateTime($xml['at']), $xml['action'], $xml['actor'], $xml['regarding'], $xml['source'], strval($xml['tags']), $xml['to'], $xml['url'],
            $found_payload);
    }
}
?>