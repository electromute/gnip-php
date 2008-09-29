<?php

/**
 * Performs Gnip subscriber related functions.
 * 
 * This class provides convenience methods for accessing the Gnip servers and
 * performing subscriber related functions.
 */
class GnipSubscriber
{
    var $helper;

    /**
     * Initialize the class.
     * 
     * @type username string
     * @param username The Gnip account username
     * @type password string
     * @param password The Gnip account 
     * 
     * Initializes a Gnip class by creating an instance of the GnipHelper 
     * class, which provides most of the basic network and time functionality.
     */
    function GnipSubscriber($username, $password)
    { 
        $this->helper = new GnipHelper($username, $password);
    }

    /**
     * Create a Gnip collection.
     * 
     * @type collection_xml xml_document
     * @param collection_xml The xml document for the collection to create
     * 
     * Creates a new collection on the Gnip server, based on the
     * passed in parameters.
     */
    function createCollection($collection_xml)
    {
        $url = $this->helper->GNIP_BASE_URL . "/collections.xml";

        return $this->helper->doHttpPost($url, $collection_xml);
    }

    /**
     * Find a Gnip collection.
     *
     * @type name string
     * @param name The name of the collection to find
     * @return string containing response from the server
     *
     */
    function findCollection($collection_name)
    {
        $url = $this->helper->GNIP_BASE_URL . "/collections/" . $collection_name . ".xml";

        return $this->helper->doHttpGet($url);
    }

    /**
     * Delete a Gnip collection.
     * 
     * @type name string
     * @param name The name of the collection to delete
     * @return string containing response from the server
     * 
     * Deletes an existing collection on the Gnip server, based on the
     * name of the collection.
     */
    function deleteCollection($name)
    {
        $url = $this->helper->GNIP_BASE_URL . "/collections/" . $name . ".xml";

        return $this->helper->doHttpDelete($url);
    }

    /**
     * Get the activity stream for a publisher.
     * 
     * @type publisher string
     * @param publisher The publisher of the data
     * @type date_and_time long
     * @param date_and_time The time for which data should be retrieved
     * @return string containing response from the server
     * 
     * Gets all of the data for a specific publisher, based on the
     * date_and_time parameter. If date_and_time is not passed in, 
     * the current time will be used. 
     * Note that all times need to be in UTC.
     */
    function get($publisher, $date_and_time=0)
    {
        if(0 == $date_and_time){
            $date_and_time = time();
        }
        $correctedTime = $this->helper->syncWithGnipClock($date_and_time);
        $timeString = $this->helper->timeToString($correctedTime);


        $url = $this->helper->GNIP_BASE_URL . "/publishers/" . $publisher .
            "/activity/" . $timeString . ".xml";

        return $this->helper->doHttpGet($url);
    }

    /**
     * Get a Gnip collection activity stream.
     * 
     * @type name string
     * @param name The name of the collection to get
     * @type date_and_time long
     * @param date_and_time The time for which data should be retrieved
     * @return string containing response from the server
     * 
     * Gets all of the data for a specific collection, based on the
     * date_and_time parameter. If date_and_time is not passed in, 
     * the current time will be used.
     * Note that all times need to be in UTC.
     */
    function getCollection($name, $date_and_time=0)
    {
        if(0 == $date_and_time){
            $date_and_time = time();
        }

        $correctedTime = $this->helper->syncWithGnipClock($date_and_time);
        $timeString = $this->helper->timeToString($correctedTime);


        $url = $this->helper->GNIP_BASE_URL . "/collections/" . $name .
            "/activity/" . $timeString . ".xml";

        return $this->helper->doHttpGet($url);
    }

    /**
     * Update a Gnip collection.
     *
     * @type collection_xml xml_document
     * @param collection_xml The xml document for the collection to be updated
     * @type name string
     * @param name The name of the collection to update
     *
     * Updates an existing collection on the Gnip server, based on
     * passed in parameters.
     */
    function updateCollection($collection_name, $collection_xml) 
    {
        $url = $this->helper->GNIP_BASE_URL . "/collections/" . $collection_name . ".xml";

        return $this->helper->doHttpPut($url, $collection_xml);
    }
};

?>
