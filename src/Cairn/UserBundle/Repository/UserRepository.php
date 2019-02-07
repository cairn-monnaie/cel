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

    public function whereReferent(QueryBuilder $qb, $userID)
    {
        $qb->join('u.referents','r')
            ->andWhere('r.id = :id')                                           
            ->setParameter('id',$userID);
        return $this;

    }

    public function findPendingUsers($referentID, $role)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRole($ub,$role)->whereReferent($ub, $referentID);
        $ub->andWhere('u.confirmationToken is NULL')     
            ->andWhere('u.enabled = false')                                    
            ->andWhere('u.lastLogin is NULL')                                  
            ->orderBy('u.name','ASC');

        return $ub->getQuery()->getResult();
    }

    public function findUsersWithStatus($referentID, $role, $isEnabled = NULL)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRole($ub,$role)->whereReferent($ub, $referentID);
        $ub->andWhere('u.confirmationtoken is null')     
            ->andWhere('u.lastLogin is not NULL')
            ->orderBy('u.name','ASC');
        if($isEnabled != NULL){
            $ub->andWhere('u.enabled = :isEnabled')
              ->setParameter('isEnabled',$isEnabled);            
        }

        return $ub->getQuery()->getResult();
    }

    public function findUsersWithPendingCard($referentID, $role)
    {
        $ub = $this->createQueryBuilder('u');                  
        $this->whereRole($ub,$role)->whereReferent($ub, $referentID);
        $ub->leftJoin('u.card','c')                                           
            ->andWhere('c.id is NULL') //card is the owning-side in the association user/card
            ->andWhere('u.confirmationtoken is null')
            ->andWhere('u.enabled = true')
            ->orderBy('u.name','ASC');                                    
        
        return $ub->getQuery()->getResult();
    }
}
