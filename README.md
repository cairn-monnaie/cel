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

  * **La carte de sécurité**   
    Il s'agit là d'un élément central dans l'utilisation de l'application. Chaque compte [e]-Cairn doit être associé à une carte de sécurité contenant des clés à 4 chiffres au format matrice papier. Lors d’actions considérées comme sensibles (changement de mot de passe, édition du profil, ajout d'un bénéficiaire, ...) , une clé de cette carte est demandée préalablement via sa position (exemple : B2). Elle sert de surcouche d'authentification avant de rediriger l'utilisateur vers l'opération initialement demandée.   
    Une carte, c'est une entité `Cairn/src/UserBundle/Entity/Card`  
    Par défaut, les cartes sont de dimensions 5x5, mais ces dimensions peuvent être modifiées via les paramètres globaux dans `app/config/parameters.yml`:
      * nombre de lignes : `cairn_card_rows : 5`
      * nombre de colonnes : `cairn_card_cols : 5`

    **Les opérations sensibles nécessitant une authentification par carte**  
      Pour déclarer une opération comme sensible, il y a 3 façons de faire :  
        * Si l'URL suffit à déclarer l'opération comme sensible (ex : changement du mot de passe), il faut ajouter la route permettant d'accéder à ladite opération dans `Cairn/src/UserBundle/Event/SecurityEvents::SENSIBLE_ROUTES`  
        * Si l'URL ne suffit pas, mais que la "sensibilité" dépend de certains paramètres de la requête (ex : virement vers un nouveau bénéficiaire, paramètre `to = new`), il faut ajouter la route permettant d'accéder à ladite opération ainsi que les paramètres déterminants dans `Cairn/src/UserBundle/Event/SecurityEvents::SENSIBLE_URLS`  
        * Si elle dépend d'une logique plus complexe nécessitant du code, il faut intégrer cette logique dans la fonction du service dédié : `Cairn/src/UserBundle/Service/Security::isSensibleOperation` 

    * _Associer son compte à une carte de sécurité_  
      En plus des clés à 4 chiffres, chaque carte de sécurité contient un code unique qui l'identifie. Pour associer son compte (nécessairement sans carte déjà associée) à une carte de sécurité, l'utilisateur saisit ce code dans le formulaire dédié.

    * _Révoquer sa carte de sécurité_  
      En cas de perte, de vol, ou de soupçon d'usurpation, l'utilisateur peut révoquer sa carte de sécurité. Cela a pour effet de dissocier la carte du compte utilisateur, puis de détruire la carte. Toutes les opérations dites "sensibles" sur la plateforme web et les opérations SMS sont alors bloquées.

    * _Demande de nouvelle carte de sécurité_  
      Un utilisateur sans carte peut demander un envoi postal d'une nouvelle carte de sécurité. Un email est alors automatiquement envoyé à l'adresse gestion afin de la notifier de cette nouvelle demande. L'email contient l'adresse renseignée par l'utilisateur. 

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
    Un bénéficiaire, c'est une entité de classe `Cairn/src/UserBundle/Entity/Beneficiary`  

  * **Crédit automatique du compte via virement Helloasso**  
    Depuis son compte [e]-Cairn, un adhérent peut réaliser un virement à l'Association sous forme de don sur Helloasso. Une notification de paiement est alors envoyée à cette application via un webhook dédié. Les informations qui y sont envoyées permettent de créditer de façon automatique le compte [e]-Cairn de l'adhérent dans la limite des [e]-cairns disponibles.  
    Si le montant du virement effectué via Helloasso est supérieur au solde du coffre [e]-Cairn  
      * seuls les [e]-cairns disponibles sont crédités sur le compte [e]-Cairn de l’adhérent
      * Un acompte est créé afin de créditer la partie restante ultérieurement (entité `Cairn/src/UserBundle/Entity/Deposit`)
      * L’adhérent est notifié par email de la création d’un acompte
      * Un email est envoyé à la gestion afin de la prévenir que le coffre [e]-Cairn est vide


  * **Paiement d'un prestataire par SMS (B2B / C2B)**  
    Tout détenteur de compte peut renseigner un numéro de téléphone et y autoriser/bloquer l'envoi de paiements par SMS depuis celui-ci.  
    Si le détenteur est un prestataire, un identifiant SMS lui est alors automatiquement attribué et permettra aux adhérents particuliers d'identifier le compte à créditer lors d'un paiement : 
    ``` 
    PAYER <montant> <IDPRO>
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
  Un groupe local est référent de tous les comptes [e]-Cairns de particuliers, ainsi que des prestataires assignés par un administrateur.  
  Cela lui permet d'avoir accès aux mêmes fonctionnalités qu'un administrateur pour la "**Gestion des membres**", mais seulement pour les particuliers et prestataires dont il est référent.  
  L'idée est que chaque groupe local ait sa propre autonomie sur la gestion des prestataires de son territoire.

### Espace Administrateur
  Un administrateur de l'application est référent de tous les autres utilisateurs de l'application. Il a donc accès à un panel de fonctionnalités liées à l'administration globale de l'application, mais aussi liées à tous ses membres.  

  * **Gestion des membres**  
    Dans l'objectif de suivre l'évolution des espaces membres, un "tableau de bord" des espaces membres est disponible afin de voir quelles opérations doivent être exécutées pour chacun des membres.  
    * _Commun à tous les membres_  
      * Accès au profil des membres
      * (Ré)Ouverture/Opposition de compte [e]-Cairn
      * Révocation de la carte de sécurité d'un membre
      * Génération d'une nouvelle carte de sécurité et association automatique
      * Association d'une carte de sécurité disponible
      * Edition de l'identifiant SMS d'un professionnel  
    * _Spécifique aux prestataires_   
      Un administrateur peut assigner/dissocier un Groupe Local à un prestataire. Le GL devient alors référent dudit prestataire, lui donnant les droits de gestion sur l'espace membre du prestataire.  

  * **Gestion des cartes de sécurité disponibles (non associées)**
    * _Générer et télécharger un ensemble de cartes_
      Un administrateur peut générer, dans un fichier ZIP, un ensemble de cartes de sécurité dans la limite du nombre de cartes autorisées. En effet, l'application définit un nombre maximal de cartes non associées afin de contrôler leur diffusion. Ce nombre est défini dans les paramères globaux dans `app/config/parameters.yml`:   
      * nombre de cartes  : `max_printable_cards : 30`

    * _Rechercher les cartes disponibles_
      Toujours dans l'objectif de suivre les cartes générées mais non associées, un "tableau de bord" des cartes non associées est disponible :  
        * Vue du nombre de cartes téléchargeables
        * Liste des cartes non associées avec leur code, leur date de génération et leur date d'expiration
        * Recherche de cartes par code / date de génération / date d'expiration
    * _Détruire une carte_
      Celle-ci devient alors indisponible. Elle ne peut plus être associée à un compte [e]-Cairn.

  * **Gestion des [e]-cairns disponibles**
    Les [e]-cairns disponibles sont stockés dans le "coffre [e]-Cairn". L'objectif est que l'administration de l'application puisse contrôler la masse de [e]-cairns disponibles pour le change  correspondant au fonds de garantie numérique. Ainsi, un "tableau de bord" du coffre[e]-Cairn est disponible, depuis lequel un administrateur peut :  
      * Voir le nombre de [e]-Cairns disponibles
      * Déclarer un nouveau nombre de [e]-Cairns disponibles
      * Voir les acomptes en attente de crédit
    * _Déclaration du nombre de [e]-cairns disponibles_ 
      L'administrateur peut déclarer une modification du solde du coffre [e]-cairns de l'application. Si des acomptes sont en attente de crédit, ceux-ci sont automatiquement exécutés suite à la modification du solde en mode FIFO si le solde le permet. 
      
      

