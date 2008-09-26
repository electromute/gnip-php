<?php

/**
 * This class provides convenience methods for accessing the Gnip servers and
 * performing publisher related functions. 
 */
class GnipPublisher
{
	var $helper;
    var $publisher;

    /**
	 * Initialize the class.
	 * 
	 * @type username string
	 * @param username The Gnip account username
	 * @type password string
	 * @param password The Gnip account password 
	 * @type publisher 
	 * @param publisher string The name of the publisher
	 * 
	 * Initializes a Gnip class by creating an instance of the GnipHelper 
	 * class, which provides most of the basic network and time functionality.
	 */
    function GnipPublisher($username, $password, $publisher)
    { 
        $this->helper = new GnipHelper($username, $password);
        $this->publisher = $publisher;
    }
};

?>