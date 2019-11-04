# Developer guide

### Table of Contents  
[Global architecture](##Global%20architecture)  
[Development](##Development)  
[Test](##Test)  
[Production](##Production)  

## Global architecture    
![global architecture](/docs/images/archi_globale.png)

## Model of object-oriented database   
![UML](/docs/images/uml.png)

In either developement or testing environment, all data are controlled for convenience.  
  * all user passwords are '@@bbccdd' 
  * all card keys are '1111'  
  * all validation codes are '1111'  

## Development
 * **Generate data**
     ```
     cd ..                                         # go to parent directory
     sudo ./setup_env.sh --env dev -d
     ```
     Cette commande, à l'heure actuelle,  ne peut pas être reproduite sur n'importe quel réseau Cyclos contenant n'importe quel jeu de données. Elle a été développée, dans un premier temps, pour les besoins du Cairn. Elle permet d'avoir une variété de données permettant de tester plein de cas différents. Il y a donc un besoin de maîtrise du script de génération de données Cyclos pour adapter celui des données Symfony. [dépôt api, branche cairn](https://github.com/cairn-monnaie/api/tree/cairn)  
     PS : Si les identifiants de l'administrateur initial ne sont pas admin_network:@@bbccdd, cette commande va échouer
     
     Une fois la génération terminée, on a tout un panel d'utilisateurs avec des données différentes (les messages de log affichés pendant l execution du script permettent d'obtenir des informations, voir les scripts pour compléter)

 * **Access applications**    
     From now on, you can access the main application, phpmyadmin and the cyclos underlying application according to their respective binding ports.
     Access front's url (main app) and connect with either :
   * Admin default credentials : admin_network : @@bbccdd
   * Adherent default credentials : for instance comblant_michel : @@bbccdd  
  
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
    NB : If you have changed some constants, **clear the cache** ! Otherwise, your changes may not be considered.
 
 * **Xdebug**  
    You can use Xdebug by setting `XDEBUG_ENABLED=true` and set `XDEBUG_REMOTE_HOST` to your local network computer ip address in your `.env` file
    Then docker build and up.
    
    /!\ The port is not `9000` (default for Xdebug) but `9001`.

##Functional Testing
### Requirements
If not done yet, repeat the [cel repo install](https://github.com/cairn-monnaie/cel/blob/master/docs/install.md) **replacing 'dev' by 'test'**  

 All the information provided in the _Development_ subsection is also relevant here.  
 Tests are achieved using phpunit, a testing framework for PHP with a built-in library for Symfony framework. 
 The source code regarding tests is available in the _tests_ directory

 * **Logs**  
    A log file is available : `docker/logs/test.log`

 * **Generating test data**  
    This will (re)create a MySQL test database from scratch
    `sudo ./setup_env.sh --env test -d`    

    **WARNING** : This command is based on many external factors which make it difficult to maintain. In the state of the git repo, and without any modification, the script should finish. It depends on :
      * the cyclos configuration : see api repo (setup.py)
      * the cyclos init data script  : see api repo (init_data.py)
      * the symfony command GenerateDatabaseCommand.php

 * **Launching tests**  
    `sudo docker-compose exec engine ./vendor/bin/phpunit --filter=...`

    NB : We **strongly recommend** not trying to launch all the tests in one command, this way : 
    `sudo docker-compose exec engine ./vendor/bin/phpunit`
    Why ? The testing environment is quite unstable and there are many requests executed, sometimes external web services & api calls. As our app is quite complex, it was very time-consuming to mock all the functions, services, so the tests work against database data, which means we do extra database queries inside the tests to ensure database integrity.
    For these reasons, we recommend to launch tests class by class, this way :
    ```
      sudo docker-compose exec engine ./vendor/bin/phpunit --filter=BankingControllerTest
      sudo docker-compose exec engine ./vendor/bin/phpunit --filter=BeneficiaryControllerTest
      ...
    ```


 * **Tests isolation**  
    In order to ensure MySQL database integrity from one test to another, any begun transaction is rolled back at the end of each test. This way, we always work with the same database content between each test. This process is automatically set up with the doctrine-test-bundle bundle.  

    **Warning** : If a test executes a transaction in the Cyclos database, a kind of dissociation between MySQL and Cyclos database may occur, as the corresponding transaction would be rolled back (see Tests isolation part above). 
 
     _Concrete example_ :  
     The user John Doe, in a functional test, has its access changed to 'disabled' state (means he cannot access the Symfony app from login/password). Our disabling process ensures the user is also disabled on Cyclos side. As the Symfony database is rolled back at test end, we finally get, at the beginning of next test :
      * John Doe is disabled on Cyclos side
      * John Doe is enabled on Symfony side

     _Workaround_ :  
     If a test executes a transaction in the Cyclos database, either :
     * explicitly commit the transaction before the end of the test 
       ```
       public function testMyTestWhichChangesCyclosDatabase()  
       {   
         // ... something thats changes the Cyclos DB state  
         \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();  
       }
       ```
       Concrete case : See the test `testChangeScheduledTransactionStatus` in `tests/UserBundle/Controller/BankingControllerTest` test class, 
     * reset Cyclos data to the state it should be before the test (if possible)
       See the test `assertUserIsDisabled` in `tests/UserBundle/Controller/BaseControllerTest` test class
       

## Production

