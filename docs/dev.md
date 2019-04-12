# Developer guide

## Global architecture
![global architecture](/docs/images/archi_globale.png)

## Model of database object-oriented
![UML](/docs/images/uml.png)

## Development
 * **Data**
     In developement (and testing) environment, all data are controlled for convenience.  
       * all user passwords are @@bbccdd
       * all card keys are 1111
       * all validation codes are 1111 
 * **Generate data**
     ```
     sudo docker-compose exec engine php bin/console cairn.user:generate-database --env=dev admin_network @@bbccdd
     ```
     Cette commande, à l'heure actuelle,  ne peut pas être reproduite sur n'importe quel réseau Cyclos contenant n'importe quel jeu de données. Elle a été développée, dans un premier temps, pour les besoins du Cairn. Elle permet d'avoir une variété de données permettant de tester plein de cas différents. Il y a donc un besoin de maîtrise du script de génération de données Cyclos pour adapter celui des données Symfony. [dépôt api, branche cairn](https://github.com/cairn-monnaie/api/tree/cairn)  
     PS : Si les identifiants de l'administrateur initial ne sont pas admin_network:@@bbccdd, cette commande va échouer
     
     Une fois la génération terminée, on a tout un panel d'utilisateurs avec des données différentes (les messages de log affichés pendant l execution du script permettent d'obtenir des informations, voir les scripts pour compléter)

 * **Access applications**    
     From now on, you can access the main application, phpmyadmin and the cyclos underlying application.  
     Access engine's url (main app) and connect with default credentials : admin_network / @@bbccdd  
     Start browsing !

 * **Update the code**  
     As a volume is mounted on the project's root directory, any change on your host machine is automatically replicated in the docker container. Hence, there is no need to rebuild or restart the engine's container.

 * **Update the database schema**  
    `sudo docker-compose exec engine doctrine:migrations:diff --env=dev`  
    `sudo docker-compose exec engine doctrine:migrations:migrate --env=dev`  

 * **Logs**  
     Using the same method of binding volumes between host and containers, all the log files are gathered in the `docker/logs` directory.  
    
 * **Emails**  
    An useful feature while developing a web app is email catching. Here, we use **mailcatcher**, listening on port 1025 to catch any message sent through our app. Therefore, we must deliver messages to port 1025 using smtp protocol.

    Open the file `app/config/parameters.yml` with your favorite editor. 
    Edit the following parameters:  
     `mailer_transport: smtp`  
     `mailer_host: email-catcher`  
     `mailer_port: 1025`    
    Now, access email-catcher's url on port 1080 to see the mailcatcher web interface. Future emails will be available there.  
 
 * **Xdebug**  
    You can use Xdebug by setting ```XDEBUG_ENABLED=true```
    and set ```XDEBUG_REMOTE_HOST``` to your local network computer ip address in your ```.env``` file
    Then docker build and up.
    
    /!\ The port is not ```9000``` (default for Xdebug) but ```9001```.

