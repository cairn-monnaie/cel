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
        $qb->andWhere('u.confirmationToken is NULL')     
            ->andWhere('u.lastLogin is not NULL'); 
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

}
