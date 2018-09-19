<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdProjectContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($rawPathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($rawPathinfo);
        $trimmedPathinfo = rtrim($pathinfo, '/');
        $context = $this->context;
        $request = $this->request;
        $requestMethod = $canonicalMethod = $context->getMethod();
        $scheme = $context->getScheme();

        if ('HEAD' === $requestMethod) {
            $canonicalMethod = 'GET';
        }


        // cairn_user_welcome
        if ('/home' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'cairn_user_welcome');
            }

            return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::indexAction',  '_route' => 'cairn_user_welcome',);
        }

        // cairn_user_install
        if ('/install' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'cairn_user_install');
            }

            return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\DefaultController::installAction',  '_route' => 'cairn_user_install',);
        }

        // cairn_user_registration
        if ('/registration' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'cairn_user_registration');
            }

            return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\DefaultController::registrationAction',  '_route' => 'cairn_user_registration',);
        }

        if (0 === strpos($pathinfo, '/register/informations')) {
            // fos_user_registration_register
            if ('/register/informations' === $trimmedPathinfo) {
                if (!in_array($canonicalMethod, array('GET', 'POST'))) {
                    $allow = array_merge($allow, array('GET', 'POST'));
                    goto not_fos_user_registration_register;
                }

                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'fos_user_registration_register');
                }

                return array (  '_controller' => 'fos_user.registration.controller:registerAction',  '_route' => 'fos_user_registration_register',);
            }
            not_fos_user_registration_register:

            // fos_user_registration_check_email
            if ('/register/informations/check-email' === $pathinfo) {
                if ('GET' !== $canonicalMethod) {
                    $allow[] = 'GET';
                    goto not_fos_user_registration_check_email;
                }

                return array (  '_controller' => 'fos_user.registration.controller:checkEmailAction',  '_route' => 'fos_user_registration_check_email',);
            }
            not_fos_user_registration_check_email:

            if (0 === strpos($pathinfo, '/register/informations/confirm')) {
                // fos_user_registration_confirm
                if (preg_match('#^/register/informations/confirm/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    if ('GET' !== $canonicalMethod) {
                        $allow[] = 'GET';
                        goto not_fos_user_registration_confirm;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'fos_user_registration_confirm')), array (  '_controller' => 'fos_user.registration.controller:confirmAction',));
                }
                not_fos_user_registration_confirm:

                // fos_user_registration_confirmed
                if ('/register/informations/confirmed' === $pathinfo) {
                    if ('GET' !== $canonicalMethod) {
                        $allow[] = 'GET';
                        goto not_fos_user_registration_confirmed;
                    }

                    return array (  '_controller' => 'fos_user.registration.controller:confirmedAction',  '_route' => 'fos_user_registration_confirmed',);
                }
                not_fos_user_registration_confirmed:

            }

        }

        elseif (0 === strpos($pathinfo, '/login')) {
            // cairn_user_redirect_login
            if (0 === strpos($pathinfo, '/login/redirection') && preg_match('#^/login/redirection/(?P<message>[^/]++)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_redirect_login');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_redirect_login')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\DefaultController::redirectToLoginAction',));
            }

            // fos_user_security_login
            if ('/login' === $pathinfo) {
                if (!in_array($canonicalMethod, array('GET', 'POST'))) {
                    $allow = array_merge($allow, array('GET', 'POST'));
                    goto not_fos_user_security_login;
                }

                return array (  '_controller' => 'fos_user.security.controller:loginAction',  '_route' => 'fos_user_security_login',);
            }
            not_fos_user_security_login:

            // fos_user_security_check
            if ('/login_check' === $pathinfo) {
                if ('POST' !== $canonicalMethod) {
                    $allow[] = 'POST';
                    goto not_fos_user_security_check;
                }

                return array (  '_controller' => 'fos_user.security.controller:checkAction',  '_route' => 'fos_user_security_check',);
            }
            not_fos_user_security_check:

        }

        // fos_user_security_logout
        if ('/logout' === $pathinfo) {
            if (!in_array($canonicalMethod, array('GET', 'POST'))) {
                $allow = array_merge($allow, array('GET', 'POST'));
                goto not_fos_user_security_logout;
            }

            return array (  '_controller' => 'fos_user.security.controller:logoutAction',  '_route' => 'fos_user_security_logout',);
        }
        not_fos_user_security_logout:

        if (0 === strpos($pathinfo, '/p')) {
            // cairn_user_password_reinitialize
            if ('/password/init' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_password_reinitialize');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\DefaultController::reinitializePasswordAction',  '_route' => 'cairn_user_password_reinitialize',);
            }

            // cairn_user_password_change
            if ('/password/new' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_password_change');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::changePasswordAction',  '_route' => 'cairn_user_password_change',);
            }

            if (0 === strpos($pathinfo, '/profile')) {
                // fos_user_profile_show
                if ('/profile' === $trimmedPathinfo) {
                    if ('GET' !== $canonicalMethod) {
                        $allow[] = 'GET';
                        goto not_fos_user_profile_show;
                    }

                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'fos_user_profile_show');
                    }

                    return array (  '_controller' => 'fos_user.profile.controller:showAction',  '_route' => 'fos_user_profile_show',);
                }
                not_fos_user_profile_show:

                // fos_user_profile_edit
                if ('/profile/edit' === $pathinfo) {
                    if (!in_array($canonicalMethod, array('GET', 'POST'))) {
                        $allow = array_merge($allow, array('GET', 'POST'));
                        goto not_fos_user_profile_edit;
                    }

                    return array (  '_controller' => 'fos_user.profile.controller:editAction',  '_route' => 'fos_user_profile_edit',);
                }
                not_fos_user_profile_edit:

            }

        }

        // cairn_user_card_security_layer
        if ('/security/card' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'cairn_user_card_security_layer');
            }

            return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::inputCardKeyAction',  '_route' => 'cairn_user_card_security_layer',);
        }

        if (0 === strpos($pathinfo, '/user')) {
            // cairn_user_referent_assign
            if (0 === strpos($pathinfo, '/user/referent/assign') && preg_match('#^/user/referent/assign/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_referent_assign');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_referent_assign')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::assignReferentAction',));
            }

            // cairn_user_users_remove
            if ('/user/remove' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_remove');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::confirmRemoveUserAction',  '_route' => 'cairn_user_users_remove',);
            }

            // cairn_user_profile_view
            if (0 === strpos($pathinfo, '/user/profile/view') && preg_match('#^/user/profile/view/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_profile_view');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_profile_view')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::viewProfileAction',));
            }

            if (0 === strpos($pathinfo, '/user/beneficiaries')) {
                // cairn_user_beneficiaries_list
                if ('/user/beneficiaries' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_beneficiaries_list');
                    }

                    return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::listBeneficiariesAction',  '_route' => 'cairn_user_beneficiaries_list',);
                }

                // cairn_user_beneficiaries_add
                if ('/user/beneficiaries/add' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_beneficiaries_add');
                    }

                    return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::addBeneficiaryAction',  '_route' => 'cairn_user_beneficiaries_add',);
                }

                // cairn_user_beneficiaries_edit
                if (0 === strpos($pathinfo, '/user/beneficiaries/edit') && preg_match('#^/user/beneficiaries/edit/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_beneficiaries_edit');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_beneficiaries_edit')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::editBeneficiaryAction',));
                }

                // cairn_user_beneficiaries_remove
                if (0 === strpos($pathinfo, '/user/beneficiaries/remove') && preg_match('#^/user/beneficiaries/remove/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_beneficiaries_remove');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_beneficiaries_remove')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::removeBeneficiaryAction',));
                }

            }

            // cairn_user_users_home
            if ('/users/home' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_home');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::usersAction',  '_route' => 'cairn_user_users_home',);
            }

            // cairn_user_email_check_validation
            if ('/user/email/check/delayed' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_email_check_validation');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::checkEmailsValidationAction',  '_route' => 'cairn_user_email_check_validation',);
            }

            // cairn_user_users_search
            if ('/user/search' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_search');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\UserController::searchUserAction',  '_route' => 'cairn_user_users_search',);
            }

        }

        elseif (0 === strpos($pathinfo, '/admin/users')) {
            // cairn_user_users_block
            if ('/admin/users/block' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_block');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::blockUserAction',  '_route' => 'cairn_user_users_block',);
            }

            // cairn_user_users_block_all
            if ('/admin/users/shutdown' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_block_all');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::shutDownAction',  '_route' => 'cairn_user_users_block_all',);
            }

            // cairn_user_users_activate
            if ('/admin/users/activate' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_activate');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::activateUserAction',  '_route' => 'cairn_user_users_activate',);
            }

            // cairn_user_users_activate_all
            if ('/admin/users/openaccess' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_users_activate_all');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\AdminController::openAccessAction',  '_route' => 'cairn_user_users_activate_all',);
            }

        }

        elseif (0 === strpos($pathinfo, '/card')) {
            // cairn_user_card_home
            if (0 === strpos($pathinfo, '/card/home') && preg_match('#^/card/home/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_home');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_card_home')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::cardOperationsAction',));
            }

            // cairn_user_card_check_activation
            if ('/card/check/delayed' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_check_activation');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::checkDelayedCardsAction',  '_route' => 'cairn_user_card_check_activation',);
            }

            // cairn_user_card_new
            if (0 === strpos($pathinfo, '/card/new') && preg_match('#^/card/new/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_new');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_card_new')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::newCardAction',));
            }

            // cairn_user_card_validate
            if ('/card/validate' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_validate');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::validateCardAction',  '_route' => 'cairn_user_card_validate',);
            }

            // cairn_user_card_revoke
            if (0 === strpos($pathinfo, '/card/revoke') && preg_match('#^/card/revoke/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_revoke');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_card_revoke')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::revokeCardAction',));
            }

            // cairn_user_card_generate
            if ('/card/generate' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_card_generate');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\CardController::generateCardAction',  '_route' => 'cairn_user_card_generate',);
            }

        }

        elseif (0 === strpos($pathinfo, '/config')) {
            // cairn_user_cyclos_config_home
            if ('/config/home' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_config_home');
                }

                return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::viewNetworkAction',  '_route' => 'cairn_user_cyclos_config_home',);
            }

            if (0 === strpos($pathinfo, '/config/sys')) {
                // cairn_user_cyclos_sysconfig_home
                if ('/config/sys' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_home');
                    }

                    return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::indexAction',  '_route' => 'cairn_user_cyclos_sysconfig_home',);
                }

                if (0 === strpos($pathinfo, '/config/sys/network')) {
                    // cairn_user_cyclos_sysconfig_network_list
                    if ('/config/sys/network/list' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_network_list');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::listNetworksAction',  '_route' => 'cairn_user_cyclos_sysconfig_network_list',);
                    }

                    // cairn_user_cyclos_sysconfig_network_add
                    if ('/config/sys/network/add' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_network_add');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::addNetworkAction',  '_route' => 'cairn_user_cyclos_sysconfig_network_add',);
                    }

                    // cairn_user_cyclos_sysconfig_network_edit
                    if (0 === strpos($pathinfo, '/config/sys/network/edit') && preg_match('#^/config/sys/network/edit/(?P<name>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_network_edit');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_sysconfig_network_edit')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::editNetworkAction',));
                    }

                    // cairn_user_cyclos_sysconfig_network_remove
                    if (0 === strpos($pathinfo, '/config/sys/network/remove') && preg_match('#^/config/sys/network/remove/(?P<name>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_network_remove');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_sysconfig_network_remove')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::removeNetworkAction',));
                    }

                    // cairn_user_cyclos_sysconfig_network_view
                    if ('/config/sys/network/view' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_network_view');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\NetworkController::viewNetworkAction',  '_route' => 'cairn_user_cyclos_sysconfig_network_view',);
                    }

                }

                elseif (0 === strpos($pathinfo, '/config/sys/group')) {
                    // cairn_user_cyclos_sysconfig_group_add
                    if (0 === strpos($pathinfo, '/config/sys/group/add') && preg_match('#^/config/sys/group/add/(?P<groupType>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_group_add');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_sysconfig_group_add')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\GroupController::addGroupAction',));
                    }

                    // cairn_user_cyclos_sysconfig_group_edit
                    if (0 === strpos($pathinfo, '/config/sys/group/edit') && preg_match('#^/config/sys/group/edit/(?P<name>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_group_edit');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_sysconfig_group_edit')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\GroupController::editGroupAction',));
                    }

                    // cairn_user_cyclos_sysconfig_group_remove
                    if (0 === strpos($pathinfo, '/config/sys/group/remove') && preg_match('#^/config/sys/group/remove/(?P<name>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_group_remove');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_sysconfig_group_remove')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\GroupController::removeGroupAction',));
                    }

                }

                elseif (0 === strpos($pathinfo, '/config/sys/product')) {
                    // cairn_user_cyclos_sysconfig_product_add
                    if ('/config/sys/product/add' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_product_add');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\ProductController::addProductAction',  '_route' => 'cairn_user_cyclos_sysconfig_product_add',);
                    }

                    // cairn_user_cyclos_sysconfig_product_edit
                    if ('/config/sys/product/edit' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_product_edit');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\ProductController::editProductAction',  '_route' => 'cairn_user_cyclos_sysconfig_product_edit',);
                    }

                    // cairn_user_cyclos_sysconfig_product_remove
                    if ('/config/sys/product/remove' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_sysconfig_product_remove');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\ProductController::removeProductAction',  '_route' => 'cairn_user_cyclos_sysconfig_product_remove',);
                    }

                }

            }

            elseif (0 === strpos($pathinfo, '/config/accounts')) {
                // cairn_user_cyclos_accountsconfig_home
                if ('/config/accounts/home' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_home');
                    }

                    return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountController::indexAction',  '_route' => 'cairn_user_cyclos_accountsconfig_home',);
                }

                if (0 === strpos($pathinfo, '/config/accounts/currency')) {
                    // cairn_user_cyclos_accountsconfig_currency_list
                    if ('/config/accounts/currency/list' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_currency_list');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\CurrencyController::listCurrenciesAction',  '_route' => 'cairn_user_cyclos_accountsconfig_currency_list',);
                    }

                    // cairn_user_cyclos_accountsconfig_currency_view
                    if ('/config/accounts/currency/view' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_currency_view');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\CurrencyController::viewCurrencyAction',  '_route' => 'cairn_user_cyclos_accountsconfig_currency_view',);
                    }

                    // cairn_user_cyclos_accountsconfig_currency_add
                    if ('/config/accounts/currency/add' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_currency_add');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\CurrencyController::addCurrencyAction',  '_route' => 'cairn_user_cyclos_accountsconfig_currency_add',);
                    }

                    // cairn_user_cyclos_accountsconfig_currency_remove
                    if ('/config/accounts/currency/remove' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_currency_remove');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\CurrencyController::removeCurrencyAction',  '_route' => 'cairn_user_cyclos_accountsconfig_currency_remove',);
                    }

                    // cairn_user_cyclos_accountsconfig_currency_edit
                    if ('/config/accounts/currency/edit' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_currency_edit');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\CurrencyController::editCurrencyAction',  '_route' => 'cairn_user_cyclos_accountsconfig_currency_edit',);
                    }

                }

                elseif (0 === strpos($pathinfo, '/config/accounts/account')) {
                    // cairn_user_cyclos_accountsconfig_account_edit
                    if ('/config/accounts/account/edit' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_account_edit');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountController::editAccountAction',  '_route' => 'cairn_user_cyclos_accountsconfig_account_edit',);
                    }

                    if (0 === strpos($pathinfo, '/config/accounts/accounttype')) {
                        // cairn_user_cyclos_accountsconfig_accounttype_home
                        if ('/config/accounts/accounttype' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_home');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::indexAction',  '_route' => 'cairn_user_cyclos_accountsconfig_accounttype_home',);
                        }

                        // cairn_user_cyclos_accountsconfig_accounttype_view
                        if (0 === strpos($pathinfo, '/config/accounts/accounttype/view') && preg_match('#^/config/accounts/accounttype/view/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_view');
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_view')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::viewAccountTypeAction',));
                        }

                        // cairn_user_cyclos_accountsconfig_accounttype_list
                        if ('/config/accounts/accounttype/list' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_list');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::listAccountTypesAction',  '_route' => 'cairn_user_cyclos_accountsconfig_accounttype_list',);
                        }

                        // cairn_user_cyclos_accountsconfig_accounttype_add
                        if (0 === strpos($pathinfo, '/config/accounts/accounttype/add') && preg_match('#^/config/accounts/accounttype/add/(?P<nature>USER|SYSTEM)/?$#s', $pathinfo, $matches)) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_add');
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_add')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::addAccountTypeAction',));
                        }

                        // cairn_user_cyclos_accountsconfig_accounttype_edit
                        if (0 === strpos($pathinfo, '/config/accounts/accounttype/edit') && preg_match('#^/config/accounts/accounttype/edit/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_edit');
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_edit')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::editAccountTypeAction',));
                        }

                        if (0 === strpos($pathinfo, '/config/accounts/accounttype/remove')) {
                            // cairn_user_cyclos_accountsconfig_accounttype_remove_confirm
                            if (0 === strpos($pathinfo, '/config/accounts/accounttype/remove/confirm') && preg_match('#^/config/accounts/accounttype/remove/confirm/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                                if (substr($pathinfo, -1) !== '/') {
                                    return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_remove_confirm');
                                }

                                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_remove_confirm')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::confirmRemoveAccountTypeAction',));
                            }

                            // cairn_user_cyclos_accountsconfig_accounttype_remove
                            if (preg_match('#^/config/accounts/accounttype/remove/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                                if (substr($pathinfo, -1) !== '/') {
                                    return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_remove');
                                }

                                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_remove')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::removeAccountTypeAction',));
                            }

                        }

                        // cairn_user_cyclos_accountsconfig_accounttype_open_confirm
                        if (0 === strpos($pathinfo, '/config/accounts/accounttype/open/confirm') && preg_match('#^/config/accounts/accounttype/open/confirm/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accounttype_open_confirm');
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_accounttype_open_confirm')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountTypeController::assignAccountTypeAction',));
                        }

                    }

                    elseif (0 === strpos($pathinfo, '/config/accounts/accountfee')) {
                        // cairn_user_cyclos_accountsconfig_accountfee_add
                        if ('/config/accounts/accountfee/add' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accountfee_add');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountFeeController::addAccountFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_accountfee_add',);
                        }

                        // cairn_user_cyclos_accountsconfig_accountfee_edit
                        if ('/config/accounts/accountfee/edit' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accountfee_edit');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountFeeController::editAccountFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_accountfee_edit',);
                        }

                        // cairn_user_cyclos_accountsconfig_accountfee_remove
                        if ('/config/accounts/accountfee/remove' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_accountfee_remove');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\AccountFeeController::removeAccountFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_accountfee_remove',);
                        }

                    }

                }

                elseif (0 === strpos($pathinfo, '/config/accounts/transfertype')) {
                    // cairn_user_cyclos_accountsconfig_transfertype_home
                    if ('/config/accounts/transfertype' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_home');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::indexAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transfertype_home',);
                    }

                    // cairn_user_cyclos_accountsconfig_transfertype_list
                    if ('/config/accounts/transfertype/list' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_list');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::listTransferTypesAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transfertype_list',);
                    }

                    // cairn_user_cyclos_accountsconfig_transfertype_view
                    if (0 === strpos($pathinfo, '/config/accounts/transfertype/view') && preg_match('#^/config/accounts/transfertype/view/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_view');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_transfertype_view')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::viewTransferTypeAction',));
                    }

                    if (0 === strpos($pathinfo, '/config/accounts/transfertype/add')) {
                        // cairn_user_cyclos_accountsconfig_transfertype_add
                        if ('/config/accounts/transfertype/add' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_add');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::addTransferTypeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transfertype_add',);
                        }

                        // cairn_user_cyclos_accountsconfig_transfertype_complete
                        if ('/config/accounts/transfertype/addFields' === $trimmedPathinfo) {
                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_complete');
                            }

                            return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::fillTransferTypeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transfertype_complete',);
                        }

                    }

                    // cairn_user_cyclos_accountsconfig_transfertype_edit
                    if (0 === strpos($pathinfo, '/config/accounts/transfertype/edit') && preg_match('#^/config/accounts/transfertype/edit/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_edit');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_transfertype_edit')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::editTransferTypeAction',));
                    }

                    // cairn_user_cyclos_accountsconfig_transfertype_remove
                    if (0 === strpos($pathinfo, '/config/accounts/transfertype/remove') && preg_match('#^/config/accounts/transfertype/remove/(?P<name>[^/]++)/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transfertype_remove');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_transfertype_remove')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferTypeController::removeTransferTypeAction',));
                    }

                }

                elseif (0 === strpos($pathinfo, '/config/accounts/transferfee')) {
                    // cairn_user_cyclos_accountsconfig_transferfee_list
                    if ('/config/accounts/transferfee/list' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transferfee_list');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferFeeController::listTransferFeesAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transferfee_list',);
                    }

                    // cairn_user_cyclos_accountsconfig_transferfee_view
                    if ('/config/accounts/transferfee/view' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transferfee_view');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferFeeController::viewTransferFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transferfee_view',);
                    }

                    // cairn_user_cyclos_accountsconfig_transferfee_add
                    if ('/config/accounts/transferfee/add' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transferfee_add');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferFeeController::addTransferFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transferfee_add',);
                    }

                    // cairn_user_cyclos_accountsconfig_transferfee_edit
                    if (0 === strpos($pathinfo, '/config/accounts/transferfee/edit') && preg_match('#^/config/accounts/transferfee/edit/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transferfee_edit');
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_cyclos_accountsconfig_transferfee_edit')), array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferFeeController::editTransferFeeAction',));
                    }

                    // cairn_user_cyclos_accountsconfig_transferfee_remove
                    if ('/config/accounts/transferfee/remove' === $trimmedPathinfo) {
                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($rawPathinfo.'/', 'cairn_user_cyclos_accountsconfig_transferfee_remove');
                        }

                        return array (  '_controller' => 'Cairn\\UserCyclosBundle\\Controller\\TransferFeeController::removeTransferFeeAction',  '_route' => 'cairn_user_cyclos_accountsconfig_transferfee_remove',);
                    }

                }

            }

        }

        elseif (0 === strpos($pathinfo, '/banking')) {
            if (0 === strpos($pathinfo, '/banking/download')) {
                // cairn_user_banking_rib_download
                if (0 === strpos($pathinfo, '/banking/download/rib') && preg_match('#^/banking/download/rib/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_rib_download');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_rib_download')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::downloadRIBAction',));
                }

                // cairn_user_banking_transfer_notice_download
                if (0 === strpos($pathinfo, '/banking/download/notice') && preg_match('#^/banking/download/notice/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transfer_notice_download');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transfer_notice_download')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::downloadTransferNoticeAction',));
                }

                // cairn_user_banking_accounts_overview_download
                if ('/banking/download/accounts' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_accounts_overview_download');
                    }

                    return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::downloadAccountsOverviewAction',  '_route' => 'cairn_user_banking_accounts_overview_download',);
                }

            }

            // cairn_user_banking_deposit_request
            if ('/banking/deposit/request' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_deposit_request');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::depositRequestAction',  '_route' => 'cairn_user_banking_deposit_request',);
            }

            // cairn_user_banking_operations
            if (0 === strpos($pathinfo, '/banking/operations') && preg_match('#^/banking/operations/(?P<type>transaction|conversion|reconversion|deposit|withdrawal)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_operations');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_operations')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::bankingOperationsAction',));
            }

            // cairn_user_banking_operation_confirm
            if (0 === strpos($pathinfo, '/banking/operation/confirm') && preg_match('#^/banking/operation/confirm/(?P<type>transaction|conversion|reconversion|deposit|withdrawal)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_operation_confirm');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_operation_confirm')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::confirmOperationAction',));
            }

            // cairn_user_banking_operations_view
            if (0 === strpos($pathinfo, '/banking/view/operations') && preg_match('#^/banking/view/operations/(?P<type>transaction|conversion|reconversion|deposit|withdrawal)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_operations_view');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_operations_view')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::viewOperationsAction',));
            }

            if (0 === strpos($pathinfo, '/banking/transaction')) {
                // cairn_user_banking_transaction_to
                if (0 === strpos($pathinfo, '/banking/transaction/new/to') && preg_match('#^/banking/transaction/new/to/(?P<frequency>unique|recurring)/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transaction_to');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transaction_to')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::transactionToAction',));
                }

                // cairn_user_banking_transaction_request
                if ('/banking/transaction/request' === $trimmedPathinfo) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transaction_request');
                    }

                    return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::transactionRequestAction',  '_route' => 'cairn_user_banking_transaction_request',);
                }

                // cairn_user_banking_transaction_recurring_cancel
                if (0 === strpos($pathinfo, '/banking/transaction/recurring/cancel') && preg_match('#^/banking/transaction/recurring/cancel/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transaction_recurring_cancel');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transaction_recurring_cancel')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::cancelRecurringTransactionAction',));
                }

                // cairn_user_banking_transaction_scheduled_changestatus
                if (0 === strpos($pathinfo, '/banking/transaction/scheduled/edit') && preg_match('#^/banking/transaction/scheduled/edit/(?P<id>\\d{19})\\.(?P<status>cancel|open|block)/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transaction_scheduled_changestatus');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transaction_scheduled_changestatus')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::changeStatusScheduledTransactionAction',));
                }

                // cairn_user_banking_transactions_recurring_view_detailed
                if (0 === strpos($pathinfo, '/banking/transactions/recurring/view/detailed') && preg_match('#^/banking/transactions/recurring/view/detailed/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transactions_recurring_view_detailed');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transactions_recurring_view_detailed')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::viewDetailedRecurringTransactionAction',));
                }

                // cairn_user_banking_transaction_occurrence_execute
                if (0 === strpos($pathinfo, '/banking/transactions/recurring/occurrence/execute') && preg_match('#^/banking/transactions/recurring/occurrence/execute/(?P<id>\\d{19})/?$#s', $pathinfo, $matches)) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transaction_occurrence_execute');
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transaction_occurrence_execute')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::executeOccurrenceAction',));
                }

            }

            // cairn_user_banking_transfer_view
            if (0 === strpos($pathinfo, '/banking/transfer/view') && preg_match('#^/banking/transfer/view/(?P<type>scheduled.past|scheduled.futur|recurring|simple)\\-(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_transfer_view');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_transfer_view')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::viewTransferAction',));
            }

            // cairn_user_banking_withdrawal_request
            if ('/banking/withdrawal/request' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_withdrawal_request');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::withdrawalRequestAction',  '_route' => 'cairn_user_banking_withdrawal_request',);
            }

            // cairn_user_banking_reconversion_request
            if ('/banking/reconversion/request' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_reconversion_request');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::reconversionRequestAction',  '_route' => 'cairn_user_banking_reconversion_request',);
            }

            // cairn_user_banking_conversion_request
            if ('/banking/conversion/request' === $trimmedPathinfo) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_conversion_request');
                }

                return array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::conversionRequestAction',  '_route' => 'cairn_user_banking_conversion_request',);
            }

            // cairn_user_banking_account_operations
            if (0 === strpos($pathinfo, '/banking/account/operations') && preg_match('#^/banking/account/operations/(?P<accountID>[^/]++)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_account_operations');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_account_operations')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::accountOperationsAction',));
            }

            // cairn_user_banking_accounts_overview
            if (0 === strpos($pathinfo, '/banking/accounts/overview') && preg_match('#^/banking/accounts/overview/(?P<id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'cairn_user_banking_accounts_overview');
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'cairn_user_banking_accounts_overview')), array (  '_controller' => 'Cairn\\UserBundle\\Controller\\BankingController::accountsOverviewAction',));
            }

        }

        // homepage
        if ('' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'homepage');
            }

            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::indexAction',  '_route' => 'homepage',);
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
