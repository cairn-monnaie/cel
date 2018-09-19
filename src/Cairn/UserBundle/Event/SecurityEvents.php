<?php
// src/Cairn/UserBundle/Event/SecurityEvents.php

namespace Cairn\UserBundle\Event;

/**                                                                            
 * This class contains events definition to be listened by our SecurityListener
 *                                                                             
 */    
final class SecurityEvents
{
    /**
     * Event to dispatch when input password is required
     */
    const INPUT_PASSWORD = 'cairn_user.input_password';

    /**
     * Event to dispatch when card key input is required
     */
    const INPUT_CARD_KEY = 'cairn_user.input_card_key';

    /**
     * List of all routes considered as sensible. An input card key will be required before accessing them. Then, if you think another
     * route in development will need to be preceded by a security card layer, just add the route to this list.
     * WARNING : ParamConverters can't be used for controller actions matching these routes. 
     */
    const SENSIBLE_ROUTES = [
        'cairn_user_card_generate',
        'cairn_user_banking_withdrawal_request',
        'cairn_user_banking_deposit_request',
        'cairn_user_banking_reconversion_request',
        'cairn_user_banking_conversion_request',
        'fos_user_profile_edit',
        'cairn_user_beneficiaries_add',
        'cairn_user_users_block_all',
        'cairn_user_users_activate_all',
        'cairn_user_users_block',
        'cairn_user_users_activate',
        'cairn_user_users_remove',
        'cairn_user_cyclos_config_home',
        'cairn_user_cyclos_accountsconfig_account_edit'
    ];

    /**
     *List of Urls considered as sensible. An input card key will be required before accessing them. The difference is that the route is
     *not enough to tell if the action is sensible or not, but depends on a parameter in the query.
     *
     */
    const SENSIBLE_URLS = [
        array('cairn_user_banking_transaction_request',array('frequency'=>'recurring','to'=>'new')),
        array('cairn_user_banking_transaction_request',array('frequency'=>'unique','to'=>'new'))
    ];
}
