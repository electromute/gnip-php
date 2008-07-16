<?php

/**
 * This class provides basic functionality help for all Gnip classes.
 */
class GnipHelper
{
    var $GNIP_BASE_URL = "https://s.gnipcentral.com";

    var $username    = "";
    var $password    = "";

    /**
	 * Initialize the class.
	 *
	 * @type username string
	 * @param username The Gnip account username
	 * @type password
	 * @param password The Gnip account password
	 *
	 * Initializes a GnipHelper class.
	 */
    function GnipHelper($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

	/**
	 * Do a HTTP GET.
	 *
	 * @type url string
	 * @param url The URL to GET
	 * @return string representing page retrieved
	 *
	 * Does a HTTP GET request of the passed in url, and returns
	 * the result from the server.
	 */
    function doHttpGet($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $loginInfo = sprintf("%s:%s",$this->username,$this->password);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $loginInfo);

		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
    }

	/**
	 * Do a HTTP POST.
	 *
	 * @type url string
	 * @param url The URL to POST to
	 * @type data string in application/x-www-form-urlencoded format
	 * @param data Formatted POST data
	 * @return string representing page retrieved
	 *
	 * Does a HTTP POST request of the passed in url and data, and returns
	 * the result from the server.
	 */
    function doHttpPost($url, $data)
	{
		$curl = curl_init();
		$loginInfo = sprintf("%s:%s",$this->username,$this->password);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		// curl_setopt($curl, CURLOPT_VERBOSE, 1);   // litter logs with crap
		curl_setopt($curl, CURLOPT_STDERR, STDOUT);  // spit the crap into stdout
		curl_setopt($curl, CURLOPT_HTTPHEADER,
		array("Content-Type: application/xml","Authorization: Basic ".base64_encode($loginInfo)));
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $loginInfo);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $response = curl_exec($curl);
	    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		    
	    switch($http_code){
		
	      case 200:
	         return $response;
             break;

	      default:
	  	      throw new Exception("HTTP Request Failed.\nHttp Status was:" . $http_code . "\nHttp Response was:" . $response . "\n");
	          break;
	    }
    }

    /**
	 * Do a HTTP PUT.
	 **/
    function doHttpPut($url, $data)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url . ";edit");
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, utf8_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/xml"));

        $loginInfo = sprintf("%s:%s",$this->username,$this->password);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $loginInfo);

		$response = curl_exec ($curl);
		curl_close ($curl);
		return $response;
    }

   /**
	 * Do a HTTP DELETE.
	 **/
    function doHttpDelete($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url . ";delete");
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/xml"));

        $loginInfo = sprintf("%s:%s",$this->username,$this->password);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $loginInfo);

		$response = curl_exec ($curl);
		curl_close ($curl);
		return $response;
    }

	/**
	 * Round time to nearest five minutes.
	 *
	 * @type theTime long
	 * @param theTime The time to round
	 * @return long containing the time at previous 5 minute mark
	 *
	 * Rounds the time passed in down to the previous 5 minute mark.
	 */
    function roundTimeToNearestFiveMinutes($theTime)
	{
        $year = (int)gmdate('Y',$theTime);
		$month = (int)gmdate('m',$theTime);
		$day = (int)gmdate('d',$theTime);
		$hour = (int)gmdate('H',$theTime);
		$min = (int)gmdate('i',$theTime);

        $newMin = $min - ($min % 5);

		// Create a new time object with the rounded minute
        return gmmktime($hour, $newMin, 0, $month, $day, $year);
	}

	/**
	 * Adjust a time so that it corresponds with Gnip time
	 *
	 * @type theTime long
	 * @param theTime The time to adjust
	 * @return long containing the corrected time
	 *
	 * This method gets the current time from the Gnip server,
	 * gets the current local time and determines the difference
	 * between the two. It then adjusts the passed in time to
	 * account for the difference.
	 */
    function syncWithGnipClock($theTime)
	{
		// Do HTTP HEAD request
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->GNIP_BASE_URL);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($curl);

		// Get local time, before we do any other processing
		// so that we can get the two times as close as possible
        $localTime = time();

		curl_close($curl);

		// Extract the time from the header
        preg_match('/.{3}, \\d{2} .{3} \d{4} \d{2}:\d{2}:\d{2} GMT/',
				   $response, $match);
		$gnipTime = strtotime($match[0]);

		// Determine the time difference
        $timeDelta = $gnipTime - $localTime;

		// Return the corrected time
        return $theTime + $timeDelta;
	}

	/**
	 * Convert the time to a formatted string.
	 *
	 * @type theTime long
	 * @param theTime The time to convert to a string
	 * @return string representing time
	 *
	 * Converts the time passed in to a string of the
	 * form YYYYMMDDHHMM.
	 */
    function timeToString($theTime)
	{
        return gmdate("YmdHi", $theTime);
	}

};
?>
