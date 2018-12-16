<?php

namespace Tests\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Tests\UserBundle\Controller\BaseControllerTest;

use Cyclos;

class SystemSetUpControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        //same username than the one provided at installation
        $adminUsername = $this->getAdminUsername();
        if($adminUsername){
            $credentials = array('username'=>$adminUsername,'password'=>'@@bbccdd');
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),
                'login',$credentials);

            //generate doctrine users or not
            $user = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'MaltOBar'));

            if(!$user){
                //            var_dump(gc_enabled());
                //            gc_enable();
                //            var_dump(gc_enabled());
                $memberGroupName = $this->container->getParameter('cyclos_group_pros');
                $adminGroupName = $this->container->getParameter('cyclos_group_network_admins');

                $memberGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($memberGroupName ,'MEMBER_GROUP');
                $adminGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($adminGroupName,'ADMIN_GROUP');

                $cyclosMembers = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($memberGroup->id);
                $cyclosAdmins = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($adminGroup->id);

                $cyclosUsers = array_merge($cyclosMembers, $cyclosAdmins);

                $superAdmin  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$adminUsername));

                $userRank = 0;
                foreach($cyclosMembers as $cyclosMember){
                    $this->createUser($cyclosMember,$superAdmin,$userRank);
                    $userRank = $userRank + 1;
                }

                $this->em->flush();

            }
        }
    }

    public function testNada()
    {
        ;
    }
}
