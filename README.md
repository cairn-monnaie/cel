[e]-Cairn
=======

# Requirements
 * docker && docker-compose
 * git

# Install

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

    Some of these parameters rely directly on the docker-compose.yml file  
      `database_host: db (name of the docker service containing MySQL database)`  
      `database_port: 3306 (port listener in the db container)`  
      `database_name: db-name`  
      `database_user: username (provided in .env)`  
      `database_password: pwd (provided in .env)`  
      `cyclos_root_prod_url: 'http://cyclos-app:8080/' (name and port of the cyclos-app docker service)`  
      `cyclos_group_pros: xxx (name of the group of professionals in your cyclos application)`  
      `cyclos_group_network_admins: 'Network administrators'`  
      `cyclos_group_global_admins: 'Global administrators'`  
      `cyclos_currency_cairn: '<currency>' (name of the currency provided in docker-compose of api application)

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

     * Build the cyclos database.  
       The cyclos-dump-minimal.sql dump file is mounted in the docker-entrypoint directory of the container. This way, the dump restore is automatically executed at container's creation.  
       `sudo docker-compose up -d cyclos-db`  

     * Check out the logs while the database is building from the basic cyclos-dump-minimal.sql file  
       `sudo docker-compose logs -f cyclos-db`  

     * One the database has been fully restored, start the cyclos app  
       `sudo docker-compose up -d cyclos-app`  

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
       `sudo docker-compose exec engine ./build-setup.sh $env admin:admin` _note_ : $env = (dev / test / prod)   
     **WARNING** : admin:admin are the credentials of the main administrator on Cyclos-side (given credentials in the cyclos-dump-minimal.sql file). In production, you must of course change them

    * Install assets
       `sudo docker-compose exec engine composer install`

     * Enable engine's user to write logs, cache files and web static files(images)  
       `sudo docker-compose exec engine chown -R www-data:www-data var web`
       
## Development

 * **Access applications**    
     From now on, you can access the main application, phpmyadmin and the cyclos underlying application.  
     Access engine's url (main app) and connect with default credentials : admin_network / @@bbccdd  
     Start browsing !

 * **Update the code**  
     As a volume is mounted on the project's root directory, any change on your host machine is automatically replicated in the container. Hence, there is no need to rebuild or restart the engine's container.

 * **Update the database schema**  
    `sudo docker-compose exec engine doctrine:migrations:migrate`  

 * **Logs**  
     Using the same method of binding volumes between host and containers, all the log files are gathered in the `docker/logs` directory.  
    
 * **Emails**  
    An useful feature while developing a web app is email catching. Here, we use **mailcatcher**, listening on port 1025 to catch any message sent through our app. Therefore, we must deliver messages to port 1025 using smtp protocol.

    Open the file `app/config/parameters.yml` with your favorite editor. 
    Update the following parameters:  
     `mailer_transport: smtp`  
     `mailer_host: email-catcher`  
     `mailer_port: 1025`    
    Now, access email-catcher's url on port 1080 to see the mailcatcher web interface. Future emails will be available there.  
 
 * **Xdebug**  
    You can use Xdebug by setting ```XDEBUG_ENABLED=true```
    and set ```XDEBUG_REMOTE_HOST``` to your local network computer ip address in your ```.env``` file
    Then docker build and up.
    
    /!\ The port is not ```9000``` (default for Xdebug) but ```9001```.
    
## Testing
      
 All the information provided in the _Development_ subsection is also relevant here. Tests are achieved using phpunit, a testing framework for PHP with a built-in library for Symfony framework. 
 The source code regarding tests is available in the _tests_ directory

 * **Logs**  
    A log file is available in `./docker/logs/test.log` file

 * **Generating test data**  
    This will (re)create a scratch MySQL test database
    `sudo docker-compose exec engine ./build-setup.sh test admin:admin`    

    `sudo docker-compose exec engine php bin/console cairn.user:generate-database --env=test admin_network @@bbccdd`
    This script first generates a set of users with an identical password : @@bbccdd, based on cyclos adherents data. 
    Then, it creates a set of security cards.
    Finally, it creates Operation entries based on Cyclos payments data. 
    **WARNING** : This command is based on many external factors which make it difficult to maintain. In the state of the git repo, and without any modification, the script should finish. It depends on :
      * the cyclos configuration : see api repo (setup.py)
      * the cyclos init data script  : see api repo (init_data.py)
      * the symfony command GenerateDatabaseCommand.php

 * **Launching tests**  
    `sudo docker-compose exec engine ./vendor/bin/phpunit`  
     The bootstrap script is automatically called when phpunit is requested. It can be found in `tests/bootstrap.php`. It executes two symfony custom console commands in order to fill the MySQL database with respect to the Cyclos database for consistency purposes. If the testing database already contains users, the command does nothing.  
      
 * **Tests isolation**  
    In order to ensure MySQL database integrity from one test to another, any begun transaction is rolled back at the end of each test. This way, we always work with the same database content between each test. This process is automatically set up with the doctrine-test-bundle bundle.  

    **Warning** : If a test executes a transaction in the Cyclos database, a kind of dissociation between MySQL and Cyclos database may occur, as the corresponding transaction would be rolled back (see Tests isolation part above). 
 
     _Example_ : The user John Doe, in a functional test, changes its password from '@@bbccdd' to '@bcdefgh'. This operation will be rolled back in MySQL database but persisted in Cyclos. Then, if you re-run the same test, it will fail because, in Cyclos, John Doe's password is not '@@bbccdd' anymore.  

     _Workaround_ : if a test executes a transaction in the Cyclos database, explicitely commit the transaction before the end of the test  
     `public function testMyTestWhichChangesCyclosDatabase()  
     {   
    // ... something thats changes the Cyclos DB state  
    \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();  
     }`
