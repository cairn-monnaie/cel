<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 03/03/19
 * Time: 09:17
 */

namespace Cairn\UserBundle\Form\DataTransformer;


use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Service\BridgeToSymfony;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Cairn\UserCyclosBundle\Service\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AccountToStringTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $cyclosBridgeSymfony;
    private $cyclosUserInfo;
    private $cyclosAccountInfo;

    public function __construct(EntityManagerInterface $entityManager, BridgeToSymfony $cyclosBridgeSymfony, UserInfo $cyclosUserInfo, AccountInfo $cyclosAccountInfo)
    {
        $this->entityManager = $entityManager;
        $this->cyclosBridgeSymfony = $cyclosBridgeSymfony;
        $this->cyclosUserInfo = $cyclosUserInfo;
        $this->cyclosAccountInfo = $cyclosAccountInfo;
    }

    /**
    * Transforms an object (AccountInfo) to a string (string).
    *
    * @param  AccountInfo|null $account
    * @return string
    */
    public function transform($account)
    {
        if (null === $account) {
            return '';
        }

        return $account->number;
    }

    /**
     * Transforms a string (name) to an object (AccountInfo).
     *
     * @param  string $autocomplete
     * @return stdClass representing org.cyclos.model.users.users.UserWithFieldsVO 
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($autocomplete)
    {
        if (!$autocomplete) {
            return '';
        }

        $userRepo = $this->entityManager
            ->getRepository(User::class);

        $re = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/';
        preg_match($re, $autocomplete, $matches, PREG_OFFSET_CAPTURE, 0);

        $user = null;
        $toUserVO = null;

        if (count($matches)>1){
            $user = $userRepo->findOneBy(array('email'=>$matches[1][0]));
        }
        if($user){
            $toUserVO = $this->cyclosUserInfo->getUserVOByKeyword($matches[1][0]);
        }else{
            $re = '/([\-]*[0-9]+)/';
            preg_match($re, $autocomplete, $matches, PREG_OFFSET_CAPTURE, 0);

            if (count($matches)>1){
                $toUserVO = $this->cyclosUserInfo->getUserVOByKeyword($matches[1][0]);
            }
        }

        
        if ($toUserVO) {
            //TODO: little hack for normalize the data to return for the OperationValidator
            $toUserVO->number = $toUserVO->accountNumber;
        }else{
            $toUserVO = $autocomplete;
        }

        //string if no object is found, object otherwise
        return $toUserVO;
    }
}
