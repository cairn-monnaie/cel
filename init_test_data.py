# coding: utf-8
from __future__ import unicode_literals

import argparse
import base64
import logging

from datetime import datetime
from datetime import timedelta

import requests
import yaml  # PyYAML
from slugify import slugify

logging.basicConfig()
logger = logging.getLogger(__name__)


def check_request_status(r):
    if r.status_code == requests.codes.ok:
        logger.info('OK')
    else:
        logger.error(r.text)
        r.raise_for_status()

def get_internal_name(name):
    name = name.replace('€', 'euro')
    slug = slugify(name)
    return slug.replace('-', '_')

# Récupération des constantes
logger.info('Récupération des constantes depuis le YAML')
APP_CONSTANTS = None
with open("app/config/parameters.yml", 'r') as app_stream:
    try:
        APP_CONSTANTS = yaml.load(app_stream)
    except yaml.YAMLError as exc:
        assert False, exc

# Ensemble des constantes nécessaires au fonctionnement du script.
LOCAL_CURRENCY_SYMBOL = 'CRN'
NAME_GROUP_PROS = str(APP_CONSTANTS['parameters']['cyclos_group_pros'])
NAME_GROUP_NETWORK_ADMINS = str(APP_CONSTANTS['parameters']['cyclos_group_network_admins'])
NAME_GROUP_GLOBAL_ADMINS = str(APP_CONSTANTS['parameters']['cyclos_group_global_admins'])
NETWORK_INTERNAL_NAME = str(APP_CONSTANTS['parameters']['cyclos_network_cairn'])
NETWORK_NAME = NETWORK_INTERNAL_NAME
LOCAL_CURRENCY_INTERNAL_NAME = str(APP_CONSTANTS['parameters']['cyclos_currency_cairn'])
LOCAL_CURRENCY_NAME = LOCAL_CURRENCY_INTERNAL_NAME
url = str(APP_CONSTANTS['parameters']['cyclos_root_test_url'])
EMAIL_DELAY = APP_CONSTANTS['parameters']['cairn_email_activation_delay']

#category for yaml file : users
constants_by_category = {}

def add_constant(category, name, value):
    if category not in constants_by_category.keys():
        constants_by_category[category] = {}
    internal_name = get_internal_name(name)
    constants_by_category[category][internal_name] = value

# Arguments à fournir dans la ligne de commande
parser = argparse.ArgumentParser()
parser.add_argument('authorization',
                    help='string to use for Basic Authentication')
parser.add_argument('--debug',
                    help='enable debug messages',
                    action='store_true')
args = parser.parse_args()

if args.debug:
    logger.setLevel(logging.DEBUG)
else:
    logger.setLevel(logging.INFO)

for k, v in vars(args).items():
    logger.debug('args.%s = %s', k, v)

# URLs des web services
global_web_services = url + 'global/web-rpc/'
network_web_services = url + NETWORK_INTERNAL_NAME + '/web-rpc/'

# En-têtes pour toutes les requêtes (il n'y a qu'un en-tête, pour
# l'authentification).
headers = {'Authorization': 'Basic ' + args.authorization}

# On fait une 1ère requête en lecture seule uniquement pour vérifier
# si les paramètres fournis sont corrects.
logger.info('Vérification des paramètres fournis...')
r = requests.post(network_web_services + 'group/search',
                  headers=headers, json={})
check_request_status(r)


########################################################################
# Création des utilisateurs pour les banques de dépôt et les comptes
# dédiés.
#
def create_user(group, name, login, email, city,  custom_values=None):
    logger.info('Création de l\'utilisateur "%s" (groupe "%s")...', name, group)
    # FIXME code à déplacer pour ne pas l'exécuter à chaque fois
    r = requests.post(network_web_services + 'group/search',
                      headers=headers, json={})
    check_request_status(r)
    groups = r.json()['result']['pageItems']
    for g in groups:
        if g['name'] == group:
            group_id = g['id']
    user_registration = {
        'group': group_id,
        'name': name,
        'username': login,
        'email': email,
        'skipActivationEmail': True,
        'passwords':{
              'assign': True ,
              'type': 'login',
              'value': '@@bbccdd',
              'confirmationValue': '@@bbccdd'
          },
        'creationDate': "2015-01-31T17:29:00",
        'addresses':[
            {
                'name': 'work',
                'addressLine1': '10 rue du test',
                'city': city,
                'defaultAddress': True,
                'hidden': False
                }
            ]

    }
#    if custom_values:
#        user_registration['customValues'] = []
#        for field_id, value in custom_values.items():
#            r = requests.get(network_web_services + 'userCustomField/load/' + field_id, headers=headers)
#            check_request_status(r)
#            custom_field_type = r.json()['result']['type']
#            if custom_field_type == 'LINKED_ENTITY':
#                value_key = 'linkedEntityValue'
#            user_registration['customValues'].append({
#                'field': field_id,
#                value_key: value,
#            })
    logger.debug('create_user : json = %s', user_registration)
    r = requests.post(network_web_services + 'user/register',
                      headers=headers,
                      json=user_registration)
    check_request_status(r)
    logger.debug('result = %s', r.json()['result'])
    user_id = r.json()['result']['user']['id']

    logger.debug('user_id = %s', user_id)
#    add_constant('users',name, user_id)
    return user_id

#create_user(
#    group='Banques de dépôt',
#    name='Crédit Agricole',
#    login='CAMPG',
#)
#create_user(
#    group='Banques de dépôt',
#    name='La Banque Postale',
#    login='LBPO',
#)


#########################################################################
## Création des utilisateurs pour les tests.
## FIXME Séparer ce code du code qui crée les données statiques.
#
## On récupère l'id du champ perso 'BDC'.
#r = requests.get(network_web_services + 'userCustomField/list', headers=headers)
#check_request_status(r)
#user_custom_fields = r.json()['result']
#for field in user_custom_fields:
#    if field['internalName'] == 'bdc':
#        id_user_custom_field_bdc = field['id']
#
#gestion_interne = {
#    'demo': 'Demo',
#    'demo2': 'Demo2',
#}
#for login, name in gestion_interne.items():
#    create_user(
#        group='Gestion interne',
#        name=name,
#        login=login,
#        password=login,
#    )
#
#bureaux_de_change = {
#    'B001': 'Euskal Moneta',
#    'B002': 'Le Fandango',
#    'B003': 'Café des Pyrénées',
#}
#for login, name in bureaux_de_change.items():
#    id_bdc = create_user(
#        group='Bureaux de change',
#        name=name + ' (BDC)',
#        login=login + '_BDC',
#    )
#    create_user(
#        group='Opérateurs BDC',
#        name=name,
#        login=login,
#        password=login,
#        custom_values={
#            id_user_custom_field_bdc: id_bdc,
#        }
#    )
#
#create_user(
#    group='Anonyme',
#    name='Anonyme',
#    login='anonyme',
#    password='anonyme',
#)

today = datetime.now()

def date_modify(nb_days):
    today = datetime.now()
    date = today + timedelta(days=nb_days)
    return date.strftime("%Y")+ '-' + date.strftime("%m")+'-'+date.strftime("%d")


pros = [
        ['maltobar', 'MaltOBar', 'maltobar@test.com','Grenoble'],
        ['labonnepioche', 'La Bonne Pioche', 'labonnepioche@test.com','Grenoble'],
        ['DrDBrew', 'DocteurD Brew Pub', 'drd_brew@test.com','Grenoble'],
        ['apogee_du_vin', 'Apogée du vin', 'apogee_vin@test.com','Grenoble'],
        ['tout_1_fromage', 'Tout un fromage', 't1f@test.com','Grenoble'],
        ['vie_integrative', 'vie intégrative', 'vie_integrative@test.com','Voiron'],
        ['denis_ketels', 'Denis Ketels', 'denis_ketels@test.com','Voiron'],
        ['nico_faus_prod', 'Nico Faus Production', 'nico_faus@test.com','Grenoble'],
        ['hirundo_archi', 'Hirundo Architecture', 'hirundo_archi@test.com','Villard-de-Lans'],
        ['maison_bambous', 'Maison aux Bambous', 'maison_bambous@test.com','Vinay'],
        ['recycleco', 'Recycleco', 'recycl_eco@test.com','Saint-Marcellin'],
        ['elotine_photo', 'Elotine Photo', 'elotine_photo@test.com','Grenoble'],
        ['boule_antan', 'La Boule d Antan', 'boule_antan@test.com','Villard-de-Lans'],
        ['la_remise', 'La Remise', 'laremise@test.com','Grenoble'],
        ['episol', 'Episol', 'episol@test.com','Grenoble'],
        ['alter_mag', 'Alter Mag', 'alter_mag@test.com','Saint-Marcellin'],
        ['verre_a_soi', 'Le Verre à soi', 'verre_soi@test.com','Bilieu'],
        ['FluoDelik', 'Fluodélik', 'fluodelik@test.com','Méaudre'],
        ['1001_saveurs', '1001 Saveurs', 'mille_saveurs@test.com','Villard-de-Lans'],
        ['belle_verte', 'La Belle Verte', 'belle_verte@test.com','Susville'],
        ['kheops_boutique', 'Khéops boutique', 'kheops_boutique@test.com','Saint-Marcellin'],
        ['ferme_bressot', 'La ferme du Bressot', 'ferme_bressot@test.com','Beaulieu'],
        ['atelier_eltilo', 'Atelier Eltilo', 'atelier_eltilo@test.com','Grenoble'],
        ['la_belle_verte', 'Belle Verte Permaculture', 'belle_verte_perma@test.com','Sillans'],
        ['mon_vrac', 'Mon Vrac', 'mon_vrac@test.com','Voiron'],
        ['le_marque_page', 'Le Marque Page', 'marque_page@test.com','Saint-Marcellin'],
        ['boutik_creative', 'La Boutik Creative', 'boutik_creative@test.com','Rives'],
        ['pain_beauvoir', 'Le Pain de Beauvoir', 'pain_beauvoir@test.com','Beauvoir-en-Royans'],
        ['la_mandragore', 'La Mandragore', 'la_mandragore@test.com','Grenoble'],
        ['jardins_epices', 'Les jardins epicés tout', 'jardins_epices@test.com','Herbeys'],
        ['lib_colibri', 'Librairie Colibri', 'librairie_colibri@test.com','Voiron'],
        ['Claire_Dode', 'La Vie Claire Dode', 'vie_claire_dode@test.com','Voiron'],
        ['fil_chantant', 'Le Fil qui Chante', 'fil_ki_chante@test.com','Voiron'],
        ['epicerie_sol', 'Epicerie Solidaire Amandine', 'epicerie_sol_amandine@test.com','Voiron'],
        ['NaturaVie', 'Naturavie', 'naturavie@test.com','Voiron'],
        ['montagne_arts', 'Les montagnarts', 'montagnarts@test.com','Valbonnais'],
        ['Biocoop', 'Biocoop', 'biocoop_chatte@test.com','Chatte'],
        ['Alpes_EcoTour', 'Alpes Ecotourisme', 'alpes_ecotourisme@test.com','Grenoble'],
        ['trankilou', 'Le Trankilou', 'trankilou@test.com','Grenoble']
]

admins = [
        ['gl_grenoble', 'Groupe Local Grenoble', 'gl_grenoble@test.com','Grenoble'],
        ['gl_voiron', 'Groupe Local Voiron', 'gl_voiron@test.com','Voiron']
]

for pro in pros:
    create_user(
        NAME_GROUP_PROS,
        pro[1],
        pro[0],
        pro[2],
        pro[3],
    )

#for admin in admins:
#    create_user(
#        NAME_GROUP_NETWORK_ADMINS,
#        admin[1],
#        admin[0],
#        admin[2],
#        admin[3],
#    )

#porteurs = {
#    'P001': 'Porteur 1',
#    'P002': 'Porteur 2',
#    'P003': 'Porteur 3',
#    'P004': 'Porteur 4',
#}
#for login, name in porteurs.items():
#    create_user(
#        group='Porteurs',
#        name=name,
#        login=login,
#    )

with open("cyclos_constants.yml", 'r') as cyclos_stream:
    try:
        CYCLOS_CONSTANTS = yaml.load(cyclos_stream)
    except yaml.YAMLError as exc:
        assert False, exc

#creation des premiers virements et génération des numéros de compte dans le fichier yaml

logger.info('Virements initiaux de 1000 u.c en ' + LOCAL_CURRENCY_INTERNAL_NAME + ' pour les grenoblois...')

for pro in pros:
#    r = requests.get(network_web_services + 'user/search',
#            headers={'Authorization': 'Basic {}'.format(base64.standard_b64encode(b'admin:admin').decode('utf-8'))},
#            json={
#                'keywords': pro[0]
#            })
#    check_request_status(r)
#    user = r.json()['result']['pageItems'][0]
#    add_constant('account_numbers',pro[1],user['id'])
#
    if pro[3] == 'Grenoble':
        r = requests.post(network_web_services + 'payment/perform',
                # on utilise ici les identifiants de l'administrateur réseau créé dans le script de config : setup_cairn_app.py
                          headers={'Authorization': 'Basic {}'.format(base64.standard_b64encode(b'admin_network:@@bbccdd').decode('utf-8'))},
                          json={
                              'type': CYCLOS_CONSTANTS['payment_types']['credit_du_compte'],
                              'amount': 1000,
                              'currency': CYCLOS_CONSTANTS['currencies'][LOCAL_CURRENCY_INTERNAL_NAME],
                              'from': 'SYSTEM',
                              'to': pro[0],
                              'description': 'dépôt'
                          })
        check_request_status(r)

logger.info('Virements initiaux de 1000 ' + LOCAL_CURRENCY_INTERNAL_NAME + ' pour les grenoblois... Terminé !')

logger.info('')
logger.info('')

logger.info('trankilou réalise un virement vers montagne_arts remettant son solde à 0')
r = requests.post(network_web_services + 'payment/perform',
        # on utilise ici les identifiants de l'administrateur réseau créé dans le script de config : setup_cairn_app.py
                  headers={'Authorization': 'Basic {}'.format(base64.standard_b64encode(b'admin_network:@@bbccdd').decode('utf-8'))},
                  json={
                      'type': CYCLOS_CONSTANTS['payment_types']['virement_inter_adherent'],
                      'amount': 1000,
                      'currency': CYCLOS_CONSTANTS['currencies'][LOCAL_CURRENCY_INTERNAL_NAME],
                      'from': 'trankilou',
                      'to': 'montagne_arts',
                      'description': 'remise à 0 du solde'
                  })
check_request_status(r)

logger.info('Remise du solde à 0... Terminé !')

logger.info('')
logger.info('')

logger.info('Virements futurs de 1 u.c en ' + LOCAL_CURRENCY_INTERNAL_NAME + ' pour 1 pro : labonnepioche vers alter_mag')

for i in range(1,10):
    r = requests.post(network_web_services + 'scheduledPayment/perform',
                      headers={'Authorization': 'Basic {}'.format(base64.standard_b64encode(b'labonnepioche:@@bbccdd').decode('utf-8'))},
                      json={
                          'type': CYCLOS_CONSTANTS['payment_types']['virement_inter_adherent'],
                          'amount': 1,
                          'currency': CYCLOS_CONSTANTS['currencies'][LOCAL_CURRENCY_INTERNAL_NAME],
                          'from': 'labonnepioche',
                          'to': 'alter_mag',
                          'firstInstallmentDate': date_modify(1),
                          'installmentsCount': 1,
                          'description': 'virement futur'
                      })
    check_request_status(r)

logger.info('Virements futurs de 1 ' + LOCAL_CURRENCY_INTERNAL_NAME + ' réalisés par labonnepioche... Terminé !')

### write account numbers and ids in a file
#with open("cyclos_constants.yml", 'w') as cyclos_stream:
#    for category in sorted(constants_by_category.keys()):
#        cyclos_stream.write(category + ':\n')
#        constants = constants_by_category[category]
#        for name in sorted(constants.keys()):
#            cyclos_stream.write('  ' + name + ': ' + constants[name] + '\n')
#cyclos_stream.close()
## Impression billets eusko
#logger.info('Impression billets mlc...')
#logger.debug(str(CYCLOS_CONSTANTS['payment_types']['impression_de_billets_' + LOCAL_CURRENCY_INTERNAL_NAME]) + "\r\n" +
#             str(CYCLOS_CONSTANTS['currencies'][LOCAL_CURRENCY_INTERNAL_NAME]) + "\r\n" +
#             str(CYCLOS_CONSTANTS['account_types']['compte_de_debit_' + LOCAL_CURRENCY_INTERNAL_NAME +'_billet']) + "\r\n" +
#             str(CYCLOS_CONSTANTS['account_types']['stock_de_billets']))
#
#r = requests.post(network_web_services + 'payment/perform',
#                  headers={'Authorization': 'Basic {}'.format(base64.standard_b64encode(b'demo:demo').decode('utf-8'))},  # noqa
#                  json={
#                      'type': CYCLOS_CONSTANTS['payment_types']['impression_de_billets_' + LOCAL_CURRENCY_INTERNAL_NAME],
#                      'amount': 126500,
#                      'currency': CYCLOS_CONSTANTS['currencies'][LOCAL_CURRENCY_INTERNAL_NAME],
#                      'from': 'SYSTEM',
#                      'to': 'SYSTEM',
#                  })
#
#logger.info('Impression billets ' + LOCAL_CURRENCY_INTERNAL_NAME + '... Terminé !')
########################################################################
logger.info('Fin du script !')
