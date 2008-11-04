<?php

/**
 * This class provides basic functionality help for all Gnip classes.
 */
class Services_Gnip_Helper
{
    private $username;
    private $password;
    private $base_url;

    function __construct($username, $password, $base_url)
    {
        $this->username = $username;
        $this->password = $password;
        $this->base_url = $base_url;
    }

    function doHttpGet($url)
    {
        return $this->doRequest($this->base_url.$url, null);
    }

    function doHttpPost($url, $data)
    {
        $this->validate($data);
        return $this->doRequest($this->base_url.$url, $data, array(CURLOPT_POST => true));
    }

    function doHttpPut($url, $data)
    {
        $this->validate($data);
        return $this->doRequest($this->base_url.$url.";edit", $data, array(CURLOPT_POST => true));
        # utf8_encode()
    }

    function doHttpDelete($url)
    {
        $this->doRequest($this->base_url.$url.";delete", null, array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => ""));
    }
    
    private function validate($xml) 
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $doc->schemaValidate(dirname(__FILE__) . '/gnip.xsd'); 
        return $xml;
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
        curl_setopt($curl, CURLOPT_URL, $this->base_url);
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
     * Convert the time to a valid bucket.
     *
     * @type theTime long
     * @param theTime The time to convert to a string
     * @return string representing time
     *
     * Converts the time passed in to a string of the
     * form YYYYMMDDHHMM.
     */
    function bucketName($time)
    {
        return gmdate("YmdHi", $time);
    }
    
    function doRequest($url, $data, $curl_options = array())
    {
        $curl = curl_init();

        $loginInfo = sprintf("%s:%s",$this->username,$this->password);
        $headers = array("Content-Type: application/xml", "User-Agent: Gnip-Client-PHP/2.0",
                         "Authorization: Basic ".base64_encode($loginInfo));
        if ($data != null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if ($curl_options[CURLOPT_PUT] != null) {
                curl_setopt($curl, CURLOPT_VERBOSE, 1);   // litter logs with crap
                $headers[] = 'Expect:';
                $headers[] = "Content-Length: ".strlen($data);
                print "\n\n---------------\n\n".$data;
            }
        }
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $loginInfo);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl, CURLOPT_VERBOSE, 1);   // litter logs with crap
        // curl_setopt($curl, CURLOPT_STDERR, STDOUT);  // spit the crap into stdout

        
        foreach ($curl_options as $option => $value) {
            curl_setopt($curl, $option, $value);
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch($http_code){
          case 200:
             return $response;
             break;

          default:
              throw new Exception("HTTP Request Failed.\n".
                                "\nURL was:".$url.
                                "\nStatus was:".$http_code.
                                "\nResponse was:".$response."\n");
              break;
        }
    }
};
?>
