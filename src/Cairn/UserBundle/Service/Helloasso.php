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
    public function __construct(array $api_consts)
    {
        $this->api_consts = $api_consts;
    }

    public function getToken()
    {
        $url = "https://api.helloasso.com/oauth2/token";

		$ch = \curl_init($url);

        $postData = [
            'client_id'=>$this->api_consts['organization']['client_id'],
            'client_secret'=>$this->api_consts['organization']['client_secret'],
            'grant_type'=>'client_credentials'
        ];

        // Set the CURL options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            )
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $results = \json_decode($json,true);

        curl_close($ch);

        if($code == 200){
            return $results['access_token'];
        }else{
            $message = (isset($results['message'])) ? $results['message'] : $results['error_description'];
            throw new \Exception($message);
        }
    }

    public function disconnect($accessToken)
    {
        return $this->get($accessToken,'oauth2/disconnect');

    }


    /**
     * Make a request to helloasso API
     *
     *@param array $params request parameters
     *@param string $resource uri matching a resource
     *@return stdClass $results result of the helloasso api request 
     */
    public function get($accessToken,$resource, $params = NULL)
    {
        $url = "https://api.helloasso.com/".$resource;
        if($params){
            $url .= "?".http_build_query($params);
        }

		$ch = \curl_init($url);

        //set BEARER TOKEN
        $headers = array(
            'authorization: Bearer '.$accessToken,
            'Content-Type: application/json'
        );

        // Set the CURL options
        $options = array(
            CURLOPT_HTTPHEADER=> $headers,
            CURLOPT_HTTPAUTH => true,
            CURLAUTH_BASIC   => true,
            CURLOPT_RETURNTRANSFER => true
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = \json_decode($json,true);

        curl_close($ch);

        if($code == 200){
            return $result;
        }else{
            $message = (isset($result['message'])) ? $result['message'] : $result['error'];
            throw new \Exception($message);
        }

    }

}
