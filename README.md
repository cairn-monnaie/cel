Digital Cairn
=======

# Requirements
 * docker && docker-compose
 * git
 * mailcatcher

# Install

## Download Sources

   `git clone https://github.com/cairn-monnaie/CairnB2B.git`

## Docker setup

 * **Set up global parameters**

    Copy the template file containing environment variables and open it with your favorite editor. 
    `cp .env.dist .env`

    Set the different variables and ports, ensuring that ports are not already inuse by another application.
    To make sure of it, you can use the following command :
    `sudo lsof -i :xxx` xxx being the port
 

    Copy the template file containing symfony app global variables and open it with your favorite editor. 
    `cp app/config/parameters.yml.dist app/config/parameters.yml`

    Some of these parameters rely directly on the docker-compose.yml file
     * database_host: db (name of the service)
     * database_port: 3306 (port listener in the db container)
     * database_name: db-name
     * database_user: username
     * database_password: pwd
     * cyclos_root_prod_url: 'http://cyclos-app:8080/'
     * cyclos_root_test_url: 'http://cyclos-app:8080/'

    Customize parameters according to your use among the following list
     * mailer_transport: smtp
     * mailer_host: 127.0.0.1
     * mailer_user: xxx (e.g admin@localhost.fr)
     * mailer_port: xxx
     * mailer_password: xxx
     * secret: ThisTokenIsNotSoSecretChangeIt
     * cairn_email_noreply: xxx (e.g noreply@localhost.fr)
     * cyclos_group_pros: **_%network-group-members%_** (step 23)
     * cyclos_group_network_admins: **_%network-group-admins%_** (step 19) 
     * cyclos_group_global_admins: **_%global-group-admins%_** (step 28)
     * cyclos_network_cairn: **_%network-internal-name%_** (step 5)
     * cyclos_currency_cairn: **_%currency-name%_** (step 8)
     * cairn_card_rows: xxx (e.g 5)
     * cairn_card_cols: xxx (e.g 5)
     * cairn_email_technical_services: xxx (e.g services@localhost.fr)
     * card_activation_delay: xxx (e.g 10)
     * cairn_default_conversion_description: xxx (e.g 'Conversion euros-cairns')
     * cairn_default_withdrawal_description: xxx (e.g 'Withdrawal cairns')
     * cairn_default_deposit_description: xxx  (e.g 'Deposit cairns')
     * cairn_default_reconversion_description: xxx (e.g 'Reconversion cairns-euros')
     * cairn_default_transaction_description: xxx (e.g 'Virement Cairn')
     * cairn_email_activation_delay: xxx (e.g 10)

 
 * **Setup the application**

     Build docker images
     `docker-compose build`

     Build the cyclos database. The cyclos-dump-minimal.sql dump file is mounted in the docker-entrypoint directory of the container.
     This way, the dump restore is automatically executed at container's creation. 
     `sudo docker-compose up -d cyclos-db`

     Check out the logs while the database is building from the basic cyclos-dump-minimal.sql file
     `docker-compose logs -f cyclos-db`

     One the database has been fully restored, start the cyclos app
     `docker-compose up -d cyclos-app`

     Then, start the database container, and check that it is listening on port 3306
     `docker-compose up -d db`
     `docker-compose logs -f db`
     Otherwise, restart the container and check again
     `docker-compose restart db`
     `docker-compose logs -f db`
  
     Start the remaining containers
     `docker-compose up -d`

     Enable engine's user to write logs, cache files and web static files(images)
     `docker-compose exec engine chown -R www-data:www-data var web`

     Install dependencies
     `docker-compose exec engine ../composer.phar update`
      
     Launch Cyclos configuration script and initialize mysql database
     `docker-compose exec engine ./build-cyclos.sh`

## Development
 * **Access applications and logs**
    
    From now on, you can access the main application, phpmyadmin and the cyclos underlying application.
    Access engine's url (main app) and connect with default credentials : admin_network / @@bbccdd
    Start browsing !

 As a volume is mounted on the project root directory, any change on the host is automatically reported in the container. Hence, there is no need to rebuild or restart the engine's container.
 * **Logs**
    Using the same method of binding volumes between host and containers, all the log files are gathered in the docker/logs directory.  
    
 * **Emails**
    An useful feature while developing a web app is email catching. Here, we use mailcatcher, listening on port 1025 to catch any message sent through our app. Therefore, we deliver messages to port 1025 using smtp protocol.

    Open the file `app/config/parameters.yml` with your favorite editor. 
    Update the following parameters:

     `* mailer_transport: smtp
     * mailer_host: email-catcher
     * mailer_port: 1025`

    Now, access email-catcher's url on port 1080 to see the mailcatcher web interface. Future emails will be available there.

## Testing
      
 All the information provided in the _Development_ subsection is also very useful for testing the app. Tests are achieved using phpunit, a testing framework for PHP with a built-in library for Symfony framework. 

 The source code regarding tests is available in the _tests_ directory, and any change will be automatically reported in the engine's container, as there is a binding volume with the project's root directory as binding point.

  * **Working with tests**

     Using mailcatcher (see _Development_ subsection), emails sent during a testing phase can be displayed.
     A log file is available : ./docker/logs/test.log

     Testing the source code
     `docker-compose exec engine ./vendor/bin/phpunit`
