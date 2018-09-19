<?php

namespace Cairn\UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;                                                 
use Cairn\UserBundle\Banknote;
use Cairn\UserBundle\BanknoteStatus;

/**
 * BanknoteRepository
 *
 */
//si tu es SUPER_ADMIN : vision de tous les bureaux de change
//si ExOFF : seulement son bureau de change
//
//Ce que j'aimerais pouvoir chercher : _le nombre de cairns ayant été stockés/mis en circulation dans un bureau de change entre 2 dates précises
//                                     _le nombre de billets ayant été stockés/mis en circulation entre 2 dates précises
//                                     _la date de dernière modif d'un billet
class BanknoteRepository extends \Doctrine\ORM\EntityRepository
{

    public function whereStatus(QueryBuilder $qb, BanknoteStatus $banknoteStatus)
    {
        $qb->andWhere('b.status = :status')
            ->setParameter('status',$banknoteStatus);
    } 
    public function whereLastUpdateBetween(QueryBuilder $qb, \Datetime $start, \Datetime $end)
    {
        $qb->andWhere('b.status.lastUpdate BETWEEN  :start AND :end') 
            ->setParameter('start',$start)
            ->setParameter('end', $end);

    }   

    public function whereCurrentMonth(QueryBuilder $qb)
    {
        $this->whereLastUpdateBetween($qb, new \Datetime(date('Y').'-'.date('m').'-01', new \Datetime(date('Y').'-'.date('m').'-t')));
    }

    public function whereValue(QueryBuilder $qb, $value)
    {
        $qb->andWhere('b.value = :value')
            ->setParameter('value', $value);
    }
}
