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
        $type = 'housenumber';

        //set latitude and longitude of new user
        //remove bis, ter from address street for localization research because it makes the research inaccurate           
        $base = strtolower(trim($address->getStreet1().' '.$address->getZipCity()->getZipCode().' '.$address->getZipCity()->getCity())) ; 
        
        //if(preg_match('/^\d+/',$base)){
        //    $type = 'housenumber';
        //}elseif(preg_match('/^(place|hameau)/',$base)){
        //    $type = 'locality';
        //}elseif(preg_match('/^(allée|allee|rue|impasse|square)/',$base)){
        //    $type = 'street';
        //}

        $base = preg_replace('/\s(bis|ter)\s/',' ',$base);
        $base = preg_replace('/^(\d+)(bis|ter)\s/','${1} ',$base);
        $base = preg_replace('/\,/',' ',$base);
        $base = preg_replace('/\s(st)e?\s/','saint',$base);
        $base = preg_replace('/\s(dr)\s/','docteur',$base);
        $base = preg_replace('/\s(jo|j\.o)\s/','jeux olympiques',$base);
        $base = preg_replace('/\s(zi)\s/','zone industrielle',$base);
        $base = preg_replace('/\s(za)\s/','zone agricole',$base);

        $arrayParams = array(                              
            'q' => $base,
            //'postcode' => $address->getZipCity()->getZipCode(),
            'lat'=>'45.19251',
            'lon'=>'5.72756',
            'type' => $type,
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
            }elseif(count($features) == 1){ 
                $location = $features[0];              
            }else{
                return array('latitude'=>NULL ,'longitude'=>NULL, 'closest'=>array('label'=>'Aucune'));
            } 

            $score = $location['properties']['score'];
            if($score <= 0.67){   
                if($score >= 0.60 && isset($location['properties']['oldcity'])){// if the address matches a former deprecated city name
                    $address->setStreet1($location['properties']['name']);
                    $address->getZipCity()->setZipCode($location['properties']['postcode']);
                    $address->getZipCity()->setCity($location['properties']['oldcity']);
                    return array('latitude'=>$location['geometry']['coordinates'][1] ,'longitude'=>$location['geometry']['coordinates'][0]);
                }
                return array('latitude'=>NULL ,'longitude'=>NULL,'closest' => $location['properties']);
            }else{
        //        $address->setStreet1($location['properties']['name']);
                $address->getZipCity()->setZipCode($location['properties']['postcode']);
                $address->getZipCity()->setCity($location['properties']['city']);
                return array('latitude'=>$location['geometry']['coordinates'][1] ,'longitude'=>$location['geometry']['coordinates'][0]);
            }
        }else{
            throw new \Exception('geolocalization_api_failed : '.$res['results']['description']);
        }

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

