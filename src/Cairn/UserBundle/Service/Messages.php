<?php
// src/Cairn/UserBundle/Service/Messages.php

namespace Cairn\UserBundle\Service;

/**                                                                            
 * The messages to deliver as a session flashbag or via an API
 *                                                                             
 */    
final class Messages
{
    public static function getMessages($requestedMessages)
    {
        if(isset($requestedMessages['key'])){
            $requestedMessages = [$requestedMessages];
        }

        foreach($requestedMessages as $index=>$message){
            $key = $message['key'];
            $args = (isset($message['args'])) ? $message['args'] : [];
            if(array_key_exists($message['key'],self::MESSAGE_KEYS)){
                $requestedMessages[$index]['message'] = vsprintf(self::MESSAGE_KEYS[$key]['message'],$args);
                $requestedMessages[$index]['type'] = self::MESSAGE_KEYS[$key]['type']; 
                $requestedMessages[$index]['args'] = $args; 
            }else{
                $requestedMessages[$index]['type'] = 'unknown'; 
                $requestedMessages[$index]['key'] = $key; 
                $requestedMessages[$index]['args'] = $args; 

                if(! isset($requestedMessages[$index]['message'])){
                    $requestedMessages[$index]['message'] = '';
                }
            }
        }

        return $requestedMessages;
    }

    /**
     * List of all the message keys
     *
     */
    const MESSAGE_KEYS = [
        ############### SERVER ERRORS ###########################
        'wrong_auth_header'=>[
            'type'=>'error',
            'message'=>'The authorization header provided does not match'
        ],
        'api_signature_format'=>[
            'type'=>'error',
            'message'=>'Wrong API header format'
        ],
        'cyclos_connection_error'=>[
            'type'=>'error',
            'message'=>'Un problème technique est apparu pendant la phase de connexion. Notre service technique en a été automatiquement informé'
        ],
        'internal_server_error'=>[
            'type'=>'error',
            'message'=>'Un problème technique inattendu est apparu. Notre service technique en a été automatiquement informé'
        ],
        'cyclos_validation_error'=>[
            'type'=>'error',
            'message'=>'Un problème technique est apparu pendant la validation de votre opération. Notre service technique en a été automatiquement informé'
        ],
        'cyclos_permission_denied'=>[
            'type'=>'error',
            'message'=>'Cette opération n\'est pas autorisée'
        ],
        'field_not_found'=>[
            'type'=>'error',
            'message'=>'%s field not found in request body'
        ],
        //'6e5212ed-a197-4339-99aa-5654798a4854'=>[
        //    'type'=>'error',
        //    'message'=>'form should not contain extra fields'
        //],

        ############### CLIENT ERRORS ###########################

        ### context errors ###
        'too_many_errors_block'=>[
            'type'=>'error',
            'message'=>'Trop d\'erreurs successives ! Votre activité a été considérée comme suspecte et votre compte a été bloqué'
        ],
        'session_expired'=>[
            'type'=>'info',
            'message'=>'Votre session a expiré. Veuillez vous reconnecter'
        ],
        'not_pro'=>[
            'type'=>'error',
            'message'=>'%s n\'est pas un professionnel'
        ],
        'reserved_for_members'=>[
            'type'=>'error',
            'message'=>'Cette action est réservée aux professionnels'
        ],
        'user_account_disabled'=>[
            'type'=>'error',
            'message'=>'Le compte utilisateur d\'identifiant %s est bloqué'
        ],
        'unique_amount_threshold'=>[
            'type'=>'error',
            'message'=>'Le montant maximum d\'un paiement unique a été dépassé(%s cairns)'
        ],
        'cumulated_amount_threshold'=>[
            'type'=>'error',    
            'message'=>'Le plafond du montant cumulé quotidien a été dépassé (%s cairns)'
        ],
        'cumulated_quantity_threshold'=>[
            'type'=>'error',
            'message'=>'Le plafond du nombre de paiements quotidien a été dépassé (%s paiements)'
        ],
        'not_referent'=>[
            'type'=>'error',
            'message'=>'Vous n\'êtes ni propriétaire ni référent de ce compte. Vous ne pouvez pas poursuivre '
        ],
        'not_access_rights'=>[
            'type'=>'error',
            'message'=>'Vous n\'avez pas les accès nécessaires pour continuer'
        ],


        ### values errors ###
        'invalid_authentification'=>[
            'type'=>'error',
            'message'=>'Les identifiants fournis ne correspondent à aucun compte'
        ],
        'too_many_chars'=>[
            'type'=>'error',
            'message'=>'%s contient trop de caractères'
        ],
        'too_few_chars'=>[
            'type'=>'error',
            'message'=>'%s ne contient pas assez de caractères'
        ],
        'amount_too_low'=>[
            'type'=>'error',
            'message'=>'%s est un montant trop faible'
        ],
        'amount_too_high'=>[
            'type'=>'error',
            'message'=>'%s est un montant trop élevé pour votre solde actuel'
        ],
        'invalid_format_value'=>[
            'type'=>'error',
            'message'=>'Le format de %s est invalide'
        ],
        'date_before_today'=>[
            'type'=>'error',
            'message'=>'La date ne peut être antérieure à celle du jour'
        ],
        'invalid_field_value'=>[
            'type'=>'error',
            'message'=>'%s est une valeur invalide'
        ],
        ### info notifs ###
        'email_validation'=>[
            'type'=>'success',
            'message'=>'Merci d\'avoir validé votre adresse électronique %s ! Vous recevrez un email lorsque l\'Association aura ouvert votre compte.'
        ],
        'sms_code_sent'=>[
            'type'=>'info',
            'message'=>'Un code de validation a été envoyé au %s'
        ],
        'notif_sent'=>[
            'type'=>'success',
            'message'=>'La notification de type %s a bien été envoyée'
        ],
        'push_pro_sent'=>[
            'type'=>'success',
            'message'=>'La notification push au sujet de %s a bien été envoyée'
        ],

        ### info on current operation ###
        #success#
        'notif_params_updated'=>[
            'type'=>'success',
            'message'=>'Vos paramètres Push ont bien été mis à jour'
        ],
        'phone_add_success'=>[
            'type'=>'success',
            'message'=>'Le téléphone %s a été ajouté avec succès'
        ],
        'phone_removal_success'=>[
            'type'=>'success',
            'message'=>'Le téléphone %s a été supprimé avec succès'
        ],
        'beneficiary_add_success'=>[
            'type'=>'success',
            'message'=>'Le bénéficiaire %s a été ajouté avec succès'
        ],
        'beneficiary_removal_success'=>[
            'type'=>'success',
            'message'=>'Le bénéficiaire %s a été supprimé de votre liste avec succès'
        ],
        'registered_operation'=>[
            'type'=>'success',
            'message'=>'Votre opération a été exécutée avec succès'
        ],

        #error#
        'inconsistent_data'=>[
            'type'=>'error',
            'message'=>'%s est considérée comme incohérente'
        ],
        'account_not_found'=>[
            'type'=>'error',
            'message'=>'Aucun compte n\'a été trouvé'
        ],
        'data_not_found'=>[
            'type'=>'error',
            'message'=>'Donnée introuvable'
        ],
        'cyclos_data_not_found'=>[
            'type'=>'error',
            'message'=>'Donnée introuvable'
        ],
        'not_enough_funds'=>[
            'type'=>'error',
            'message'=>'Vous n\'avez pas les fonds nécessaires'
        ],
        'cancel_button'=>[
            'type'=>'info',
            'message'=>'Cette opération a été annulée'
        ],
        'wrong_code_cancel'=>[
            'type'=>'error',
            'message'=>'3 erreurs à la suite, l\'opération a été annulée'
        ],
        'wrong_code'=>[
            'type'=>'error',
            'message'=>'Code de confirmation incorrect'
        ],
        'remaining_tries'=>[
            'type'=>'error',
            'message'=>'%s essai(s) restant(s)'
        ],
        'operation_timeout'=>[
            'type'=>'error',
            'message'=>'Le délai a expiré'
        ],
        'geolocalization_failed'=>[
            'type'=>'error',
            'message'=>'La géolocalisation a échoué pour l\'adresse %s'
        ],
        'repeat_password_fail'=>[
            'type'=>'error',
            'message'=>'Les champs ne correspondent pas'
        ],
        #info#
        'account_still_assoc_phone'=>[
            'type'=>'info',
            'message'=>'Un compte [e]-Cairn est toujours associé au numéro %s'
        ],
        'operation_already_processed'=>[
            'type'=>'info',
            'message'=>'Cette opération a déjà été enregistrée'
        ],
        'already_in_use'=>[
            'type'=>'info',
            'message'=>'Déjà utilisé'
        ],
        'fos_user.email.already_used'=>[
            'type'=>'info',
            'message'=>'Email déjà utilisé'
        ],
        'beneficiary_already_reg'=>[
            'type'=>'info',
            'message'=>'%s fait déjà partie de vos bénéficiaires enregistrés'
        ],
        'account_already_blocked'=>[
            'type'=>'info',
            'message'=>'Le compte associé à %s est déjà bloqué'
        ],

        ############## CLIENT MESSAGES ##########################
        'maintenance_state'=>[
            'type'=>'error',
            'message'=>'Le serveur est en état de maintenance'
        ],
        'sms_payment_authorized'=>[
            'type'=>'info',
            'message'=>'%s peut désormais payer par SMS'
        ],
        'sms_payment_unauthorized'=>[
            'type'=>'info',
            'message'=>'%s ne peut plus payer par SMS'
        ],
        'pro_and_person_assoc_phone'=>[
            'type'=>'info',
            'message'=>'Le numéro %s est associé à un compte pro et un compte particulier'
        ],
        'missing_value'=>[
            'type'=>'error',
            'message'=>'%s est manquant pour continuer cette opération'
        ]
    ];


}
