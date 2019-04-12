# Installation guide

## Requirements
 * docker && docker-compose
 * git
 * Install [api repo](https://github.com/cairn-monnaie/api/tree/cairn)

## Download Sources

   `git clone https://github.com/cairn-monnaie/CairnB2B.git`

## Docker setup

 * **Set up global parameters**

    Copy the template file containing environment variables and open it with your favorite editor.   
      `cp .env.dist .env`

    Set the different variables and ports, ensuring that ports are not already in use by another application.
    To make sure of it, you can use the following command :  
      `sudo lsof -i :xxx` xxx being the port
 
    Copy the template file containing symfony app global variables and open it with your favorite editor.  
      `cp app/config/parameters.yml.dist app/config/parameters.yml`

    Some of these parameters rely directly on this `docker-compose.yml` file and the [api repo](https://github.com/cairn-monnaie/api/tree/cairn) docker_compose.yml   
      `database_host: db (name of the docker service containing MySQL database)`  
      `database_port: 3306 (port listener in the db container)`  
      `database_name: db-name`  
      `database_user: username (provided in .env)`  
      `database_password: pwd (provided in .env)`  
      `cyclos_root_prod_url: 'http://cyclos-app:8080/' (name and port of the cyclos-app docker service)`  
      `cyclos_group_pros: xxx (name of the group of professionals in your cyclos application)`  
      `cyclos_group_network_admins: 'Network administrators'`  
      `cyclos_group_global_admins: 'Global administrators'`  
      `cyclos_currency_cairn: '<currency>' (name of services.api.environment.CURRENCY_SLUG  in docker-compose of api repo)`

    Customize parameters according to your use among the following list
      `mailer_transport: smtp`  
      `mailer_host: engine (name of the docker service executing php code)`  
      `mailer_user: xxx (e.g admin@localhost.fr)`  
      `mailer_port: xxx`  
      `mailer_password: xxx`  
      `secret: ThisTokenIsNotSoSecretChangeIt`  
      `cairn_email_noreply: xxx (e.g noreply@localhost.fr)`  
      `cairn_card_rows: xxx (e.g 5)`  
      `cairn_card_cols: xxx (e.g 5)`  
      `cairn_email_technical_services: xxx (e.g services@localhost.fr)`  
      `card_activation_delay: xxx (e.g 10)`  
      `cairn_default_conversion_description: xxx (e.g 'Conversion euros-cairns')`  
      `cairn_default_withdrawal_description: xxx (e.g 'Withdrawal cairns')`  
      `cairn_default_deposit_description: xxx  (e.g 'Deposit cairns')`  
      `cairn_default_reconversion_description: xxx (e.g 'Reconversion cairns-euros')`  
      `cairn_default_transaction_description: xxx (e.g 'Virement Cairn')`  
      `cairn_email_activation_delay: xxx (e.g 10)`  

 * **Setup the application**

     * Build docker images   
       `sudo docker-compose build`

     * Then, start the database container, and check that it is listening on port 3306  
       `sudo docker-compose up -d db`  
       `sudo docker-compose logs -f db`   
       Otherwise, restart the container and check again  
       `sudo docker-compose restart db`  
       `sudo docker-compose logs -f db`  
  
     * Start the remaining containers  
       `sudo docker-compose up -d`  

     * Install dependencies from composer.json 
       `sudo docker-compose exec engine composer update`  
      
     * Change the set of cities   
       By default, the web/zipcities.sql file contains cities of Isère (French department). Following the exact same format, replace its content with your custom set of cities.

     * Launch Cyclos configuration script and initialize mysql database  
       `sudo docker-compose exec engine ./build-setup.sh $env` _note_ : $env = (dev / test / prod)   

     Cette commande, dans un environnement de dev/test, va créer une base de données vide, créer le schéma de BDD à partir des migrations et, finalement, créer un ROLE\_SUPER\_ADMIN avec les identifiants de l'admin réseau Cyclos par défaut : (login = admin\_network et pwd = @@bbccdd )
     
     
    * Install assets
       `sudo docker-compose exec engine composer install`

     * Enable engine's user to write logs, cache files and web static files(images)  
       `sudo docker-compose exec engine chown -R www-data:www-data var web`

