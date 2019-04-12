[e]-Cairn
=======

# Espace membre [e]-Cairn
Bienvenue sur la page github du [e]-Cairn, une application symfony développée pour la numérisation du Cairn, la monnaie locale de Grenoble.
Ce code est à l'initiative du [Cairn, Monnaie Locale Complémentaire et Citoyenne](https://www.cairn-monnaie.com/)
![login page](/docs/images/CEL_connexion.png)

## Installation guide       
  Check out the installation guide [here]()  
## Testing
      
 All the information provided in the _Development_ subsection is also relevant here. Tests are achieved using phpunit, a testing framework for PHP with a built-in library for Symfony framework. 
 The source code regarding tests is available in the _tests_ directory

 * **Logs**  
    A log file is available in `./docker/logs/test.log` file

 * **Generating test data**  
    This will (re)create a scratch MySQL test database
    `sudo docker-compose exec engine ./build-setup.sh test`    

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
