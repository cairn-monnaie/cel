Digital Cairn
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

    Customize parameters according to your use among the following list
      `mailer_transport: smtp`  
      `mailer_host: engine (name of the docker service executing php code)`  
      `mailer_user: xxx (e.g admin@localhost.fr)`  
      `mailer_port: xxx`  
      `mailer_password: xxx`  
      `secret: ThisTokenIsNotSoSecretChangeIt`  
      `cairn_email_noreply: xxx (e.g noreply@localhost.fr)`  
      `cyclos_group_pros: xxx (e.g Adherents)`  
      `cyclos_group_network_admins: xxx (e.g Network Admins)`  
      `cyclos_group_global_admins: xxx (e.g Global Admins)`  
      `cyclos_network_cairn: xxx (e.g network)` **MUST BE SLUGIFIED**  
      `cyclos_currency_cairn: xxx (e.g euro)` **MUST BE SLUGIFIED**  
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
       `docker-compose build`

     * Build the cyclos database.  
       The cyclos-dump-minimal.sql dump file is mounted in the docker-entrypoint directory of the container. This way, the dump restore is automatically executed at container's creation.  
       `sudo docker-compose up -d cyclos-db`  

     * Check out the logs while the database is building from the basic cyclos-dump-minimal.sql file  
       `docker-compose logs -f cyclos-db`  

     * One the database has been fully restored, start the cyclos app  
       `docker-compose up -d cyclos-app`  

     * Then, start the database container, and check that it is listening on port 3306  
       `docker-compose up -d db`  
       `docker-compose logs -f db`   
       Otherwise, restart the container and check again  
       `docker-compose restart db`  
       `docker-compose logs -f db`  
  
     * Start the remaining containers  
       `docker-compose up -d`  

     * Enable engine's user to write logs, cache files and web static files(images)  
       `docker-compose exec engine chown -R www-data:www-data var web`  

     * Install dependencies from composer.json 
       `docker-compose exec engine ../composer.phar update`  
      
     * Change the set of cities   
       By default, the web/zipcities.sql file contains cities of Isère (French department). Following the exact same format, replace its content with your custom set of cities.

     * Launch Cyclos configuration script and initialize mysql database  
       `docker-compose exec engine ./build-setup.sh env admin:admin`  
     **WARNING** : admin:admin are the credentials of the main administrator on Cyclos-side (given credentials in the cyclos-dump-minimal.sql file). In production, you must of course change them

## Development

 * **Access applications and logs**    
     From now on, you can access the main application, phpmyadmin and the cyclos underlying application.  
     Access engine's url (main app) and connect with default credentials : admin_network / @@bbccdd  
     Start browsing !

 * **Update the code**  
     As a volume is mounted on the project's root directory, any change on your host machine is automatically replicated in the container. Hence, there is no need to rebuild or restart the engine's container.

 * **Update the database schema**  
    `docker-compose exec engine doctrine:migrations:diff`  
    `docker-compose exec engine doctrine:migrations:migrate`  

 * **Logs**  
     Using the same method of binding volumes between host and containers, all the log files are gathered in the docker/logs directory.  
    
 * **Emails**
    An useful feature while developing a web app is email catching. Here, we use **mailcatcher**, listening on port 1025 to catch any message sent through our app. Therefore, we must deliver messages to port 1025 using smtp protocol.

    Open the file `app/config/parameters.yml` with your favorite editor. 
    Update the following parameters:  
     `mailer_transport: smtp`
     `mailer_host: email-catcher`
     `mailer_port: 1025`  
    Now, access email-catcher's url on port 1080 to see the mailcatcher web interface. Future emails will be available there.  

## Testing
      
 All the information provided in the _Development_ subsection is also very useful for testing the app. Tests are achieved using phpunit, a testing framework for PHP with a built-in library for Symfony framework. 

 The source code regarding tests is available in the _tests_ directory, and any change will be automatically replicated in the engine's container, as there is a binding volume with the project's root directory as binding point.

  * **Working with tests**

     Using mailcatcher (see _Development_ subsection), emails sent during a testing phase can be displayed.
     A log file is available : ./docker/logs/test.log

     Generating test data from scratch on Cyclos-side  (should be done after installation)
     `python init_test_data.py  \`echo -n admin_network:@@bbccdd | base64 \``
     This script first generates a set of users with an identic password : @@bbccdd.
     Then, it credits some users with 1000 units of account (all  the users in a given city : Grenoble by default) 
     Finally, a specified user (labonnepioche by default) makes some scheduled payments.

     Testing the source code
     `docker-compose exec engine ./vendor/bin/phpunit`
     The bootstrap is automatically called when phpunit is requested : tests/bootstrap.php. It fills the MySQL database with respect to the Cyclos database for consistency purposes, using a symfony custom console command. If the testing database already contains users, the command does nothing.

     Tests isolation
     In order to ensure MySQL database integrity, any begun transaction is rolled back at the end of each test. This way, we always work with the same database content between each test. This process is automatically set up with the doctrine-test-bundle bundle.

     **Warning**
     If a test executes a transaction in the Cyclos database, a kind of dissociation between MySQL and Cyclos database may occur, as the corresponding transaction would be rolled back (see Tests isolation part above).
     Example : The user John Doe, in a functional test, changes its password from @@bbccdd to @bcdefgh. This operation will be rolled back in MySQL database but persisted in Cyclos. Then, if you re-run the same test, it will fail because, in Cyclos, John Doe's password is not @@bbccdd anymore.
     Workaround : if a test executes a transaction in the Cyclos database, expicitely commit the transaction before the end of the test
     `public function testMyTestWhichChangesCyclosDatabase()
     {
    // ... something thats changes the Cyclos DB state
    \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();
     }`
