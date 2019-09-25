<?php
// src/Cairn/UserBundle/Validator/MandateValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Repository\MandateRepository;
use Cairn\UserBundle\Entity\Mandate;

use Doctrine\ORM\EntityManager;


class MandateValidator extends ConstraintValidator
{

    public function __construct(MandateRepository $mandateRepo)
    {
        $this->mandateRepo = $mandateRepo;
    }

    /**
     * Validates the provided mandate
     *
     */
    public function validate($mandate, Constraint $constraint)
    {
        if($mandate->getAmount() < 0.01){
            $this->context->buildViolation('Montant trop faible : doit être supérieur à 0.01')
                ->atPath('amount')
                ->addViolation();
        }

        $today = new \Datetime();

        if(! $mandate->getID()){
            if($today->diff($mandate->getBeginAt())->invert == 1){
                $this->context->buildViolation("La date de début du mandat doit être ultérieure à la date du jour")
                    ->atPath('beginAt')
                    ->addViolation();
            }
        }

         $interval = $mandate->getBeginAt()->diff($mandate->getEndAt());
         if($interval->invert == 1 || ($interval->invert == 0 && $interval->days == 0)){
            $this->context->buildViolation("Période invalide : la date de fin est antérieure à la date de début")
                ->atPath('beginAt')
                ->addViolation();
            return;
         }

        $interval = $mandate->getBeginAt()->diff($mandate->getEndAt());

        if($interval->m < 6){
            $this->context->buildViolation("Période invalide : l'engagement doit être d'au moins 6 mois")
                ->atPath('endAt')
                ->addViolation();
        }

        $limit = 25;
        if($mandate->getBeginAt()->format('d') > $limit){
            $this->context->buildViolation("Pour éviter toute confusion, la date doit être comprise du 1 au ".$limit)
                ->atPath('beginAt')
                ->addViolation();
        }

        if($mandate->getEndAt()->format('d') > $limit){
            $this->context->buildViolation("Pour éviter toute confusion, la date doit être comprise du 1 au ".$limit)
                ->atPath('endAt')
                ->addViolation();
        }

        $contractor = $mandate->getContractor();

        if(! $contractor->isAdherent()){
            $this->context->buildViolation("Le mandataire doit être un adhérent")
                ->atPath('contractor')
                ->addViolation();
            return;
        }

        //look for an ongoing mandate
        $mb = $this->mandateRepo->createQueryBuilder('m');
        $this->mandateRepo->whereContractor($mb, $contractor)->whereStatus($mb, array(Mandate::UP_TO_DATE,Mandate::OVERDUE));
        $ongoingMandate = $mb->getQuery()->getOneOrNullResult();

        //look for a scheduled mandate
        $mb = $this->mandateRepo->createQueryBuilder('m');
        $this->mandateRepo->whereContractor($mb, $contractor)->whereStatus($mb, array(Mandate::SCHEDULED));
        $futureMandate = $mb->getQuery()->getOneOrNullResult();


        if($ongoingMandate && ($ongoingMandate !== $mandate)){
            //if there is an ongoing mandate, you can only declare a future mandate 
            $diff = $mandate->getBeginAt()->diff($ongoingMandate->getEndAt());

            if($diff->invert == 0){
                $this->context->buildViolation("Un mandat se terminant le ".$ongoingMandate->getEndAt()->format('d-m-Y') ." est déjà en cours")
                    ->atPath('beginAt')
                    ->addViolation();
                return;
            }
        }

        if($futureMandate && ($futureMandate !== $mandate)){
            $this->context->buildViolation("Un futur mandat commençant le ".$futureMandate->getBeginAt()->format('d-m-Y') ." est déjà prévu")
                ->atPath('beginAt')
                ->addViolation();
            return;
        }

        if($mandate->getMandateDocuments()->count() == 0){
            $this->context->buildViolation("Aucun document fourni")
                ->atPath('mandateDocuments')
                ->addViolation();
            return;
        }

    }
}
