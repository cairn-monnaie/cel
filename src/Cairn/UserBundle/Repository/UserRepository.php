<?php                                                                          

namespace Cairn\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;                                                 

/**                                                                            
 * UserRepository                                                              
 *                                                                             
 */                                                                            
class UserRepository extends EntityRepository                    
{                                                                              

    public function findAdminWithCity($city)
    {
        $qb = $this->createQueryBuilder('u');                  
        $qb->join('u.address','addr')
            ->join('addr.zipCity','zp')
            ->where('zp.city = :city')
            ->setParameter('city',$city)
            ->andWhere('u.roles LIKE :roles') 
            ->setParameter('roles','%"'.'ROLE_ADMIN'.'"%')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

    public function whereWillBeSerialized(QueryBuilder $qb)
    {
        $qb
            ->leftJoin('u.address','addr')->addSelect('addr')
            ->leftJoin('addr.zipCity','zc')->addSelect('zc')
            ->leftJoin('u.smsData','sD')->addSelect('sD')
            ->leftJoin('sD.phones','p')->addSelect('p')
            ->leftJoin('u.image','i')->addSelect('i')
            //->leftJoin('u.notificationData','n')->addSelect('n')
            //->leftJoin('u.apiClient','a')->addSelect('a')
            ;

        return $this;
    }

    public function myFindByRole($roles)                                        
    {                                                                          
        $qb = $this->createQueryBuilder('u');                                  

        foreach($roles as $role){
            $this->whereRole($qb,$role);
        }

        $qb->orderBy('u.name','ASC'); 

        return $qb->getQuery()->getResult();                                   
    }                                                                          

    public function whereRole(QueryBuilder $qb, $role)
    {
        $qb->andWhere('u.roles LIKE :roles') 
            ->setParameter('roles','%"'.$role.'"%');
        return $this;
    }

    public function whereRoles(QueryBuilder $qb, $roles)
    {
        $conditions = array();
        foreach($roles as $role){
            $conditions[] = "u.roles LIKE '%".$role."%'";
        }

        $orX = $qb->expr()->orX();
        $orX->addMultiple($conditions);
        $qb->andWhere($orX);

        return $this;
    }

    public function whereKeywords(QueryBuilder $qb, $keywords)
    {
        $conditions = array();
        foreach($keywords as $keyword){
            $conditions[] = "u.keywords LIKE '%".$keyword."%'";
        }
        foreach($keywords as $keyword){
            $conditions[] = "u.description LIKE '%".$keyword."%'";
        }

        $orX = $qb->expr()->orX();
        $orX->addMultiple($conditions);
        $qb->andWhere($orX);

        return $this;
    }

    public function whereAdherent(QueryBuilder $qb)
    {
        $roles = array('ROLE_PRO','ROLE_PERSON');
        return $this->whereRoles($qb, $roles);
    }

    public function whereReferent(QueryBuilder $qb, $userID)
    {
        $qb->join('u.referents','r')
            ->andWhere('r.id = :id')                                           
            ->setParameter('id',$userID);
        return $this;
    }

    /**
     * if isEnabled = false, we make a difference between opposed user and the case where user wants to be removed
     */
    public function whereEnabled(QueryBuilder $qb, $isEnabled)
    {
        $qb->andWhere('u.enabled = :enabled')                                           
            ->setParameter('enabled',$isEnabled);
        
        if(! $isEnabled){
            $this->whereToRemove($qb,false); 
        }
        return $this;
    }

    public function whereConfirmed(QueryBuilder $qb)
    {
        $qb->andWhere('u.mainICC is not NULL');

        return $this;
    }

    public function wherePublish(QueryBuilder $qb,$publish)
    {
        $qb->andWhere('u.publish = :publish')
            ->setParameter('publish',$publish);

        return $this;
    }

    public function whereProCategoriesSlugs(QueryBuilder $qb,$categorySlugs)
    {
        if(empty($categorySlugs)){
            return $this;
        }

        $qb->innerJoin('u.proCategories','pc')->andWhere($qb->expr()->in('pc.slug', $categorySlugs));

        return $this;
    }

    public function wherePending(QueryBuilder $qb)
    {
        $qb->andWhere('u.confirmationToken is NULL')     
            ->andWhere('u.enabled = false')                                    
            ->andWhere('u.lastLogin is NULL'); 
        return $this;
    }

    public function whereToRemove(QueryBuilder $qb, $toRemove)
    {
        $qb->andWhere('u.removalRequest = :toRemove')
            ->setParameter('toRemove', $toRemove);                                           

        return $this;
    }

    public function findPendingUsers($referentID, $role)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRoles($ub, array($role) )->whereReferent($ub, $referentID)->wherePending($ub);
        $ub->orderBy('u.name','ASC');

        return $ub->getQuery()->getResult();
    }

    public function findUsersWithStatus($referentID, $role, $isEnabled = NULL)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRoles($ub, array($role) )->whereReferent($ub, $referentID)->whereConfirmed($ub);
        $ub->orderBy('u.name','ASC');
        if($isEnabled !== NULL){
            $this->whereEnabled($ub, $isEnabled);
        }

        return $ub->getQuery()->getResult();
    }

    public function findUsersWithPendingCard($referentID, $role)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRoles($ub, array($role) )->whereReferent($ub, $referentID)->whereEnabled($ub,true);
        $ub->leftJoin('u.card','c')                                           
            ->andWhere('c.id is NULL') //card is the owning-side in the association user/card
            ->orderBy('u.name','ASC');                                    
        
        return $ub->getQuery()->getResult();
    }

    public function findUsersByPhoneNumber($phoneNumber)
    {
        $ub = $this->createQueryBuilder('u');                  

        $ub->leftJoin('u.smsData','s') //smsData is the owning-side in the association user/smsData                                
            ->join('s.phones','p')
            ->andWhere('p.phoneNumber = :number') 
            ->setParameter('number',$phoneNumber)
            ->addSelect('s');

        return $ub->getQuery()->getResult();
    }

    public function findOneByAccessToken($token)
    {
        $ub = $this->createQueryBuilder('u');                  

        $ub->join('u.apiClient','a') //smsData is the owning-side in the association user/smsData                                
            ->andWhere('a.accessToken = :token') 
            ->setParameter('token',$token);

        return $ub->getQuery()->getOneOrNullResult();
    }

    /**                                                                        
     * Calculates extrema coordinates around a point with given distance       
     *
     * Here, we are forced to use a raw SQL query instead of DQL or QueryBuilder because the numerical spatial function st_distance_sphere
     * (which computes spherical distance between two geometric points) is not handled by Doctrine Mysql as it is a PostGreSQL feature
     *                                                                         
     *@param float $lat latitude of central point (degrees)                    
     *@param float $lon longitude of central point (degrees)                   
     *@param float $dist distance (km)                                         
     *@param array $extrema set of extrema coordinates to match given distance
     *@param boolean $proOnly Return only users such that role is ROLE_PRO or not     
     *@return array of Users 
     */ 
    public function getUsersAround($lat, $lon, $dist, $extrema, $proOnly = true)
    {
        $subSql = 'roles LIKE "%ROLE_PRO%"';
        $subSql = ($proOnly) ? $subSql : $subSql.' OR roles LIKE "%ROLE_PERSON%" ';
        $conn = $this->getEntityManager()->getConnection();                                          
        $sql = '                                                               
            SELECT name,email,description,address_id,a.longitude,a.latitude,st_distance_sphere(point(:lon, :lat),point(a.longitude, a.latitude))/1000 AS distance 
            FROM cairn_user u 
            LEFT JOIN address a ON u.address_id = a.id
            WHERE ('.$subSql.')
            AND (a.longitude > :minLon AND a.longitude < :maxLon             
            AND a.latitude < :maxLat AND a.latitude > :minLat)                 
            HAVING distance < :dist                                            
            ORDER BY distance
        ';
        $stmt = $conn->prepare($sql);                                          
        $stmt->execute(
            array(
                'minLon' => $extrema['minLon'] , 'maxLon' => $extrema['maxLon'],
                'minLat' => $extrema['minLat'] , 'maxLat' => $extrema['maxLat'] ,
                'lon' => $lon,
                'lat'=>$lat,
                'dist'=>$dist
            ));
        return $stmt->fetchAll();
    }

}
