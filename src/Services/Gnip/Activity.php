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


	/**
     * Constructor.
     * 
     * @param string or DateTime $at
	 * @param string $action
	 * @param string $actor
	 * @param string $regarding
	 * @param string $source
	 * @param string $tags
	 * @param string $to
	 * @param string $url
	 * @param object $payload optional Services_Gnip_Payload 
     * 
     * Creates a Services_Gnip_Activity object. Tags should be a comma-delimited string
	 * of items. The payload object is optional.
	 * 
     */
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
    

	/**
     * To XML.
     * 
     * @return XML formatted activity data
	 *
     * Converts the activity to properly formatted XML.
     */
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
    

	/**
     * From XML.
     * 
	 * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule
	 *
     * Converts XML formatted rule to Services_Gnip_Activity object.
     */
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