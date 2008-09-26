<?php
/**
 *  Extended to provide commmon XML serialization/customizations for Gnip
 * 
 */
class GnipSimpleXMLElement extends SimpleXMLElement
{
    public function addOptionalAttribute($name, $value)
    {
        if(! empty($value)) {
          parent::addAttribute($name, $value);
        }
    }

	public function addOptionalChild($name, $value)
    {
        if(!empty($value)) {
          parent::addChild($name, $value);
        }
    }
    
    public function asXML()
    {
       return str_replace(PHP_EOL,'',str_replace('<?xml version="1.0"?>','', parent::asXML()));
    }
}
?>