<?php                                                                          
// src/Cairn/UserBundle/Service/Helloasso.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Event\SecurityEvents;

/**
 * This class contains services related to Helloasso
 *
 */
class Helloasso
{
    public function __construct(string $api_login, string $api_password)
    {
        $this->api_login = $api_login;
        $this->api_password = $api_password;

    }


    /**
     * Make a request to helloasso API
     *
     *@param array $params request parameters
     *@param string $resource uri matching a resource
     *@return stdClass $results result of the helloasso api request 
     */
    public function get($resource, $params = NULL)
    {
        $url = "https://api.helloasso.com/v3/".$resource.".json";

        if($params){
            $url = $url."?".http_build_query($params);
        }

		$ch = \curl_init($url);
        
        // Set the CURL options
        $options = array(
            CURLOPT_HTTPAUTH => true,
            CURLAUTH_BASIC   => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->api_login.":".$this->api_password
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $results = \json_decode($json);

        curl_close($ch);

        return $results;

    }

}
