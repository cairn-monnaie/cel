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

    public function whereReferent(QueryBuilder $qb, $userID)
    {
        $qb->join('u.referents','r')
            ->andWhere('r.id = :id')                                           
            ->setParameter('id',$userID);
        return $this;

    }


    public function whereRole(QueryBuilder $qb, $role)
    {
        $qb->andWhere('u.roles LIKE :roles') 
            ->setParameter('roles','%"'.$role.'"%');
        return $this;
    }
}
