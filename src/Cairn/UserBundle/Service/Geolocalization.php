<?php                                                                          
// src/Cairn/UserBundle/Service/Geolocalization.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Entity\Address;

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

    /**
     * Get latitude and longitude coordinates of an user's address
     *
     *@param Address $address  geopoint coordinates
     *@return array
     */
    public function getCoordinates(Address $address)
    {
        //set latitude and longitude of new user           
        $arrayParams = array(                              
            'q' => $address->getStreet1(),                 
            'postcode' => $address->getZipCity()->getZipCode(),
            'type' => 'housenumber',
            'limit' => 2                                   
        );                                                 

        $res = $this->api->get('https://api-adresse.data.gouv.fr/','search/',$arrayParams);

//        var_dump($res['results']['features']);
        if($res['code'] == 200){ 
            $features = $res['results']['features'];       

            if( count($features) > 1){ 
                if($features[0]['properties']['score'] > $features[1]['properties']['score'] ){
                    $location = $features[0];              
                }else{
                    $location = $features[1];              
                }
            }elseif(count($features) == 1){ 
                $location = $features[0];              
            }else{
                return array('latitude'=>NULL ,'longitude'=>NULL, 'closest'=>'aucune');
            } 

            if($location['properties']['score'] <= 0.6){   
                return array('latitude'=>NULL ,'longitude'=>NULL,'closest' => $location['properties']['label']);
            }else{
                return array('latitude'=>$location['geometry']['coordinates'][1] ,'longitude'=>$location['geometry']['coordinates'][0]);
            }
        }

        return array('latitude'=>NULL ,'longitude'=>NULL, 'closest'=>'aucune');
;

    }

    /**
     * Calculates distance from two geopoints with latitude and longitude
     *
     *@param float $lat1 latitude of first point
     *@param float $lon1 longitude of first point
     *@param float $lat2 latitude of second point
     *@param float $lon2 longitude of second point
     *@return float
     */
    function getDistance($lat1, $lon1, $lat2, $lon2) {
        if(!$lat1 || !$lat2 || !$lon1 || !$lon2){
            return NULL;
        }

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            return ($miles * 1.609344);
        }
    }

    /**
     * Calculates extrema coordinates around a point with given distance
     *
     * Those extrema define a rectangle of coordinates beyond which the distance to central point is necessarily bigger than 
     * the given distance. This is used as an optimization hint to avoid computing distance from all users but only those whose address
     * are inside this rectangle of coordinates
     *
     *@param float $lat latitude of central point (degrees)
     *@param float $lon longitude of central point (degrees)
     *@param float $dist distance (km)
     *@return float
     */
    function getExtremaCoords($lat, $lon, $dist){
        if(!$lat || !$lon){
            return NULL;
        }

        $dist = abs($dist);

        $deltaLat = $dist / 111.1;
        $deltaLon = $dist / (deg2rad(1) * 6371 * cos(deg2rad($lat) )) ;

        return array('minLat'=>$lat - $deltaLat, 'maxLat'=>$lat + $deltaLat, 'minLon'=>$lon - $deltaLon, 'maxLon'=>$lon + $deltaLon);
    }
}

