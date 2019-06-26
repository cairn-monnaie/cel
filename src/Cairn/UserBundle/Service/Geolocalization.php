<?php                                                                          
// src/Cairn/UserBundle/Service/Geolocalization.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Entity\User;

/**
 * This class contains all useful services related to users locations
 *
 */
class Geolocalization
{
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function getCoordinates(User $user)
    {
        $address = $user->getAddress();
        //set latitude and longitude of new user           
        $address = $user->getAddress();                    
        $arrayParams = array(                              
            'q' => $address->getStreet1(),                 
            'postcode' => $address->getZipCity()->getZipCode(),
            'limit' => 2                                   
        );                                                 

        $res = $this->api->get('https://api-adresse.data.gouv.fr/','search/',$arrayParams);

        if($res['code'] == 200){                           
            $features = $res['results']['features'];       
            if( count($features) > 1){                     
                if($features[0]['properties']['score'] > $features[1]['properties']['score'] ){
                    $location = $features[0];              
                }else{                                     
                    $location = $features[1];              
                }                                          
            }else{                                         
                $location = $features[0];                  
            }                                              

            if($location['properties']['score'] <= 0.6){   
                return NULL;
            }else{
                return array('latitude'=>$location['geometry']['coordinates'][1] ,'longitude'=>$location['geometry']['coordinates'][0]);
            }
        }

        return NULL;

    }
}

