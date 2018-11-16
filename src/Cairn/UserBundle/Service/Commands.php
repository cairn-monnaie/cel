<?php                                                                          
// src/Cairn/UserBundle/Service/Commands.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class Commands
{
    protected $em;

    protected $messageNotificator;

    protected $templating;

    protected $cardActivationDelay;

    protected $emailValidationDelay;

    protected $router;

    public function __construct(EntityManager $em, MessageNotificator $messageNotificator, TwigEngine $templating, $cardActivationDelay, $emailValidationDelay, Router $router)
    {
        $this->em = $em;
        $this->messageNotificator = $messageNotificator;
        $this->templating = $templating;
        $this->cardActivationDelay = $cardActivationDelay;
        $this->emailValidationDelay = $emailValidationDelay;
        $this->router = $router;
    }

    /**
     * searches new registered users whom emails have not been confirmed, warns them or remove them
     *
     * Everyday, this action is requested to look for registered users who have not validated their email. A delay to do so is defined.
     * If the deadline is missed, the new registered user is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     */
    public function checkEmailsValidation()
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $ub = $userRepo->createQueryBuilder('u');
        $ub->where('u.enabled = false')
           ->andWhere('u.lastLogin is NULL')
            ->andWhere('u.confirmationToken is not NULL')
            ;

        $pendingUsers = $ub->getQuery()->getResult();

        $from = $this->messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));

        foreach($pendingUsers as $user){
            $creationDate = $user->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ '.$this->emailValidationDelay.' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->emailValidationDelay,30);
            if( ($interval->invert == 0) && ($diff != 0)){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Validation de votre adresse email';
                        $confirmationUrl = $this->router->generate('fos_user_registration_confirm',
                            array('token'=>$user->getConfirmationToken()) );

                        $body = $this->templating->render('CairnUserBundle:Emails:reminder_email_activation.html.twig',
                            array('email'=>$user->getEmail(),'remainingDays'=>$diff,'confirmationUrl'=>$confirmationUrl));

                        $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);

                    }
                }
            }
            else{
                $subject = 'Expiration de validation';
                $body = $this->templating->render('CairnUserBundle:Emails:email_expiration.html.twig',array('diff'=>$diff));

                $params = new \stdClass();                                             
                $params->status = 'REMOVED';                                           
                $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

                $userManager = new UserManager();
                $userManager->changeStatusUser($params);
                $saveEmail = $user->getEmail();
                $this->em->remove($user);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);

            }

        }
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
                        $body = $this->templating->render('CairnUserBundle:Emails:reminder_card_activation.html.twig',array('card'=>$card,'remainingDays'=>$diff));
                        $this->messageNotificator->notifyByEmail($subject,$from,$card->getUser()->getEmail(),$body);

                    }

                }
            }
            else{
                $subject = 'Expiration de votre carte de sécurité Cairn';
                $body = $this->templating->render('CairnUserBundle:Emails:expiration_card.html.twig',array('card'=>$card,'diff'=>$diff));
                $card->getUser()->setCard(NULL);
                $saveEmail = $card->getUser()->getEmail();
                $this->em->remove($card);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);
            }
        }
    }

}
