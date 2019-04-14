[e]-Cairn
=======

# Espace  [e]-Cairn
Bienvenue sur la page github du [e]-Cairn, une application symfony développée pour la numérisation du Cairn, la monnaie locale du bassin de vie Grenoblois.  
Ce code est à l'initiative du [Cairn, Monnaie Locale Complémentaire et Citoyenne](https://www.cairn-monnaie.com/)
![login page](/docs/images/CEL_connexion.png)

## Installation guide       
  Check out the installation guide [here](https://github.com/cairn-monnaie/cel/blob/master/docs/install.md)  

## Developer guide       
  Check out the developer guide [here](https://github.com/cairn-monnaie/cel/blob/master/docs/dev.md)  

## Fonctionnalités du [e]-Cairn

### Espace membre
  * **Virement compte à compte**
    Depuis son compte [e]-Cairn, un adhérent peut réaliser un virement en identifiant le bénéficiaire par son numéro de compte ou son adresse email. Ces virements peuvent être à exécution immédiate ou différée.

  * **Consultation du compte**
    * _Aperçu du compte_  
      Les comptes sont listés avec leur type, leur solde ainsi que leur capacité de dépense

    * _Filtrage des opérations_  
      Toutes les opérations sont répertoriées et paginées. Vous pouvez alors appliquer des filtres pour ne visualiser que les opérations qui vous intéressent :  
        * type (virement, paiement SMS, dépôt, retrait, …)
        * montant
        * mot-clé dans la description ou le motif court
      
  * **Téléchargement de documents**  
    * _Relevé de compte_  
      Le relevé de compte peut être téléchargé dans deux formats différents (PDF / CSV), et peut être filtré par dates de début et de fin.
      
    * _Avis d'opération_
      Ce document, téléchargeable au format PDF, contient les données identifiant les comptes débiteur et créditeur, ainsi que le montant du virement, la date d’exécution et un identifiant unique associé au transfert. Le statut du virement peut être "Exécuté", "A exécuter" ou "Echoué".

  * **Gestion de ses bénéficiaires**
    Vous pouvez enregistrer des bénéficiaires (via email ou numéro de compte) afin de ne plus avoir à saisir leurs informations manuellement à chaque virement. De plus, une fois un bénéficiaire enregistré, la case "validation de votre identité par carte de sécurité" n'est plus nécessaire.

  * **Crédit automatique du compte via virement Helloasso**
    Depuis son compte [e]-Cairn, un adhérent peut réaliser un virement à l'Association sous forme de don sur Helloasso. Une notification de paiement est alors envoyée à cette application via un webhook dédié. Les informations qui y sont envoyées permettent de créditer de façon automatique le compte [e]-Cairn de l'adhérent dans la limite des [e]-cairns disponibles.

  * **Paiement d'un prestataire par SMS (B2B / C2B)**
    Tout détenteur de compte peut renseigner un numéro de téléphone et y autoriser/bloquer l'envoi de paiements par SMS depuis celui-ci.  
    Si le détenteur est un prestataire, un identifiant SMS lui est alors automatiquement attribué et permettra aux adhérents particuliers d'identifier le compte à créditer lors d'un paiement : 
    ``` 
    PAYER 15 <IDPRO>
    ```
  * **Gestion de l'espace membre**
    * _Les données personnelles_
        * Nom d'utilisateur (généré automatiquement à l'ouverture de compte et non éditable) 
        * adresse email  (éditable)
        * Nom de la structure (resp. Nom et prénom) pour un prestataire (resp. particulier) (non  éditable)
        * Description (éditable)
        * Adresse : rue + code postal + ville (éditable)
        * Logo (pour les prestataires uniquement)  
    * _Les données SMS_
        * Un numéro de téléphone si l'utilisateur souhaite pouvoir réaliser des opérations par SMS
        * Autoriser/Bloquer la **réalisation** d'opérations par SMS
        * Autoriser/Bloquer la **réception** de paiements par SMS (pour les prestataires uniquement)
        * Un identifiant SMS associé  au numéro de téléphone (pour les prestataires uniquement)  
    * _Le mot de passe_  
        Evidemment, l'utilisateur peut changer son mot de passe, et est obligé de le faire à la 1ère connexion, car un mot de passe temporaire est fourni à l'ouverture de compte.
    * _Opposition de compte_
        L'utilisateur peut bloquer son propre espace membre. Le compte est dès lors inaccessible, et les éventuelles opérations SMS sont automatiquement bloquées. 
    * _Clôture de compte_
        L'utilisateur peut demander la clôture de son compte [e]-Cairn. Celle-ci ne sera prise en compte que si tous ses comptes sont soldés. La fermeture effective doit être ensuite effectuée par un administrateur.
### Espace Groupe Local

### Espace Administrateur
