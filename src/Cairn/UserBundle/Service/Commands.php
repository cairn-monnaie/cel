<?php                                                                          
// src/Cairn/UserBundle/Service/Commands.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Cairn\UserBundle\Service\MessageNotificator;

class Commands
{
    protected $em;

    protected $messageNotificator;

    protected $templating;

    protected $cardActivationDelay;

    public function __construct(EntityManager $em, MessageNotificator $messageNotificator, TwigEngine $templating, $cardActivationDelay)
    {
        $this->em = $em;
        $this->messageNotificator = $messageNotificator;
        $this->templating = $templating;
        $this->cardActivationDelay = $cardActivationDelay;
    }

    public function checkEmailsValidation()
    {

    }

    /**
     * searches users with unactivated cards, warns them or remove their card
     *
     * Everyday, this action is requested to look for users who have not activated their card. A maximal delay is defined.
     * If the deadline is missed, the new user's card is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     */
    public function checkCardsActivation()
    {

        $cardRepo = $this->em->getRepository('CairnUserBundle:Card');

        $cb = $cardRepo->createQueryBuilder('c');
        $cb->join('c.user','u')
            ->where('c.enabled = false')
            ->andWhere('c.generated = true')
            ->addSelect('u');
        $cards = $cb->getQuery()->getResult();

        $from = $this->messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));
        foreach($cards as $card){
            $creationDate = $card->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ '.$this->cardActivationDelay.' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->cardActivationDelay,30);
            if( ($interval->invert == 0) && ($diff != 0)){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Activation de votre carte de sécurité Cairn';
                        $body = $this->templating->renderView('CairnUserBundle:Emails:reminder_card_activation.html.twig',array('card'=>$card,'remainingDays'=>$diff));
                        $this->messageNotificator->notifyByEmail($subject,$from,$card->getUser()->getEmail(),$body);

                    }

                }
            }
            else{
                $subject = 'Expiration de votre carte de sécurité Cairn';
                $body = $this->templating->renderView('CairnUserBundle:Emails:expiration_card.html.twig',array('card'=>$card,'diff'=>$diff));
                $card->getUser()->setCard(NULL);
                $saveEmail = $card->getUser()->getEmail();
                $this->em->remove($card);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);
            }
        }
    }

}
