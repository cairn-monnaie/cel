Digital Cairn
=======

# Requirements
 * composer https://getcomposer.org/download/
 * git
 * docker
# Install

 Throughout this document, some notations will be used and need to be defined :

   * xxx : fill with whatever you want, it will not be needed later on 
   * **_$variable_** : line that you need to enter or customize,  it will be reused later on
   * **_%variable%_** : line already entered previously in the tutorial

 You will find these notations either in instructions or in command lines.

## Download Sources

   `git clone https://github.com/cairn-monnaie/CairnB2B.git`
## Get a Cyclos license  
 * Register at cyclos license server [here](https://license.cyclos.org/app/guest/register)
 * note login and password (noted $license-login and $license-password respectively)
 * more details [here](https://license.cyclos.org/)
## Install Cyclos on a Debian based system 
 * use docker https://hub.docker.com/r/cyclos/cyclos/

  `docker network create cyclos-net`
 
  `docker run -d --name=cyclos-db --net=cyclos-net --hostname=cyclos-db -e POSTGRES_DB=cyclos -e POSTGRES_USER=$cyclos_user -e POSTGRES_PASSWORD=$cyclos_password cyclos/db`

  The tag '4.8.2' enforces docker to install this specific version. It is chosen because there exists a script to clean the database from users and transactions which works only on this version, pretty useful in test environment.

  `docker run -d --name=cyclos-app -p 1234:8080 --net=cyclos-net -e DB_HOST=cyclos-db -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=%cyclos_password% cyclos/cyclos:4.8.2`


## Configure a Cyclos instance

 1. Reach your cyclos instance at example.com:1234 (the first time, it can take several minutes to start)
 2. **Cyclos license server authentication**

     * Login name : **_%license-login%_**(provided while registering a cyclos license)
     * Password : **_%license-password%_**(provided while registering a cyclos license)
     * Click next
 3. **Basic configuration**

     * Application name (name of your cyclos instance) : xxx
     * localization : xxx
     * Click next
 4. **System administrator** 

     These are the profile fields and credentials of the main administrator of the application, so fill them
     carefully.
     * Name : xxx
     * Login name : **_$admin-login_**
     * E-Mail : **_$admin-email_**
     * Password : **_$admin-password_**
     * Confirm password : **_$admin-password_**
     * Click finish
 5. **Basic network information**

     * Name : **_$network-name_**
     * Internal name : xxx (not empty)
     * Description : xxx
     * Click next
 6. **Localization**

     * Customize localization : leave it unchecked if you want to use default ones (defined at step 3)
     * Click next
 7. **Administrator** 

     * create a network administrator : uncheck
     * click next
 8. **Currency**

     * Currency name : **_$currency-name_**
     * Currency symbol : xxx
     * Currency symbol : xxx
     * click next 
 9. **System accounts**

     * Unlimited account : check and **_$debit-account_**
     * System account : **_$system-account_**
     * Additional system account : uncheck
     * Click next
 10. **User account** 

     * User account : **_$user-account_**
     * Default negative balance limit : even if filled with 0, refill it with 0(sign "-" visible)
     * Initial credit : xxx
     * Click next
 11. **Brokers** 

     * Setup brokers : uncheck
     * Click next
 12. **Profile fields** 

     * Leave all fields unchecked
     * Click next 
 13. **References** 

     * Check "not used"
     * Click next 
 14. **Records**

     * Leave all fields unchecked
     * Click next 
 15. **Message categories**

     * Click next 
 16. **Advertisments**

     * Click Finish 
 17. **Network details** 

     * Enabled : make sure it is checked
     * Click "switch to this network"
 18. **Configure the currency to suit the application** 

     _Access : System(top tab) / Account Configuration(bold in left menu) / Currencies_
     * Click on the currency( created at step 8) 
     * Decimal places : 2
     * Enable transfer number : check
     * Transfer number identifier length : 8
     * WARNING : NO prefix/suffix !
     * Click save
 19. **Configure permissions of network administrators' group**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups / Network Administrators_
     * Name : **_$network-group-admins_** (by default Network administrators)
     * Internal name : xxx
     * Click save
     * Click Permissions(top-right tab on group screen)
     * General :
        * My profile fields : enabled/registration/visible/editable : check for full name / login name / email
        * Manage my channels access : check
     * System Accounts : 
        * system accounts : check all
        * system to system payments : check all
        * system to user payments : check all
        * system recurring payments : check view + cancel
        * system scheduled payments : check view + cancel + block + unblock + process installment + settle installment
     * User management :
        * user registration : check
        * login users via web services : check
        * user channels access : select "manage"
     * User Accounts : 
        * access user accounts : check all
        * payments as user to user : check all
        * payments as user to system : check all
        * recurring payments : check view + cancel
        * scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
        * Accounts balance limits : select "manage"
     * Click save

 20. **Configure a transfer type from an existing account type**

     _Access : System(top tab) / Account configuration(bold in left menu) / Account types_
     * Click on **_%user-account%_** (defined in step 10)
     * Click on "Transfer types" (top-right tab on account type screen)
     * Click on the first transfer type (from **_%user-account%_** to **_%debit-account%_**)
     * Enabled : check
     * Channels : check main web + web services + Mobile app
     * Allow recurring payments : check
     * Allow scheduled payments : check
     * Max installments on scheduled payments : 1
     * Click save
 
 21. **Repeat step 20 for all transfer types in _%user-account%_ account type**
 22. **Repeat step 20 and 21 for all account types (_%debit-account%_ / _%system-account%_)**

 23. **Configure group of members**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups_
     * Click on group "Users"(unique member group)
     * Enabled : check
     * Name : **_$network-group-members_** (by default Users)
     * Click save

 24. **Configure the Product associated with user Account Type %_user-account%_**

     _Access : System(top tab) / User Configuration(bold in left menu) / Products
     * Click on the only product (Members)
     * Name : fill with **_%user-account%_** name
     * Internal name : fil with **_%user-account%_** internal name
     * Accounts 
        * User account : must contain **_%user-account%_**
        * Default negative balance limit :  refill with 0 (sign "-" must be visible)
        * system payments : check all
        * user payments : check all
        * recurring payments : check view + cancel
        * scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
     * Click save
 25. **Check product's assignation to Member group**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups_
     * click on  **_%network-group-members%_** (defined in step 23)
     * click on Products (top-right tab of the group screen)
     * check that the created product appears in "Products assigned to Group" table (should be assigned by default)
 26. **Configure Global Administration's channels** 

     * **main web channel**

       _Access : Switch to Global administration (top-side on the screen)_

       _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_
       * Click on "Global default" configuration
       * Click on channels (top-right of configuration screen) 
       * Click on "main web"
       * session timeout : xxx
       * Click save

     * **web services channel**

       _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_
       * Click on "Global default" configuration
       * Click on channels (top-right of configuration screen) 
       * Click on "web services"
       * Enabled : check
       * User access : select "Enforced enabled"
       * session timeout : xxx
       * Click save
 27. **Change usernames configuration**
        
      For now, the application sets up login name lengths straight in the source code, and is not customizable. If you want to change them,you will have to modify the source code in the UserValidation class (\Cairn\UserBundle\Validator\UserValidator) and use exactly the same values.

      _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_
      * Click on "Global default" configuration
      * Login name length : 5 to 16
      * Click save

 27. **Change password type configuration**

      For now, the application sets up password lengths straight in the source code, and is not customizable. If you want to change them,you will have to modify the source code in the UserValidation class (\Cairn\UserBundle\Validator\UserValidator) and use exactly the same values.

     _Access : System (top tab) / User Configuration(bold in left menu) / Password types_
     * Click on login password
     * password length :  8 to 25 
     * Disallow obvious password :  check
     * Avoid repeated passwords : check
     * Expires after : 100 years (we don't deal with password expiration)
     * Click save
 28. **Configure group of global administrators**

     _Access : System (top tab) / User Configuration(bold in left menu) / Groups_
     * Click on "Global administrators"
     * Name : **_$global-group-admins_** 
     * Click save
 29. **Save the configuration into a backup file**

     As you experienced, configuring a Cyclos instance is a really long process, and there are even much more functionalities that are not dealt with in the scope of this application. For this reason, having a backup sql file with the configuration saved is necessary.
     * `docker exec -i -u postgres cyclos-db pg_dump cyclos > cyclos-dump.sql` 

     This way, if you have another instance of Cyclos to configure, you may just restore it using this backup file (see here for details https://hub.docker.com/r/cyclos/cyclos/)

## Install symfony project
 * `composer install`

 * **Create SQL User and grant permissions on prod-dev/test databases**

    The created user will have access merely to the application databases
    * `mysql -u root -p`
    * `CREATE USER '$sql-username'@'localhost' IDENTIFIED BY '$sql-password';`
    * `GRANT ALL PRIVILEGES ON '$database-name' . * TO '%sql-username%'@'localhost';`
    * `GRANT ALL PRIVILEGES ON '$database-test-name' . * TO '%sql-username%'@'localhost';`
    * `FLUSH PRIVILEGES;`

 * **Provide global parameters**

   During this step, you will provide some global parameters that the application needs. Be careful, you will need data provided during cyclos installation steps

   `php $PATH/composer.phar update`

     * database_host: 127.0.0.1
     * database_port: null
     * database_name: **_%database-name%_**
     * database_test_name: **_%database-test-name%_**
     * database_user: **_%sql-username%_**
     * database_password: **_%sql-password%_**
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
     * cyclos_network_cairn: **_%network-name%_** (step 5)
     * cyclos_currency_cairn: **_%currency-name%_** (step 8)
     * cyclos_global_admin_username: **_%admin-login%_** (step 4)
     * cyclos_global_admin_password: **_%admin-password%_** (step 4)
     * cyclos_global_admin_email: **_%admin-email%_** (step 4)
     * cairn_card_rows: xxx (e.g 5)
     * cairn_card_cols: xxx (e.g 5)
     * cairn_email_technical_services: xxx (e.g services@localhost.fr)
     * cyclos_root_prod_url: 'http://example.com:1234/'
     * cyclos_root_test_url: 'http://example.com:1235/'
     * card_activation_delay: xxx (e.g 10)
     * cairn_default_conversion_description: xxx (e.g 'Conversion euros-cairns')
     * cairn_default_withdrawal_description: xxx (e.g 'Withdrawal cairns')
     * cairn_default_deposit_description: xxx  (e.g 'Deposit cairns')
     * cairn_default_reconversion_description: xxx (e.g 'Reconversion cairns-euros')
     * cairn_default_transaction_description: xxx (e.g 'Virement Cairn')
     * cairn_email_activation_delay: xxx (e.g 10)


 * **Create Symfony database**
    
    Initialize the database with entities
    * `php bin/console doctrine:database:create`
    * `php bin/console doctrine:schema:update --force`

    Import cities with respective zipcodes in Isère (French department). Change entries according to your localization
    * `php bin/console doctrine:database:import web/zipcities.sql`

 * **Create initial administrator**
     * visit "example.com/install". 
       This creates a user on Doctrine's side with profile fields of cyclos global administrator
     
 * **Access application with admin credentials**
     * visit "example.com/login"
     * login with installed admin credientials **_%admin-login%_** and **_%admin-password%_** and start browsing !  

# Testing #
## Install ##
 * **Install docker images**
    * `docker run -d --name=cyclos-db-test --net=cyclos-net --hostname=cyclos-db-test -e POSTGRES_DB=cyclos -e POSTGRES_USER=%cyclos_user% -e POSTGRES_PASSWORD=%cyclos_password% cyclos/db`
    
    The tag '4.8.2' enforces docker to install this specific version. It is chosen because there exists a script to clean the database from users and transactions which works only on this version, pretty useful while testing.
    * `docker run -d --name=cyclos-app-test -p 1235:8080 --net=cyclos-net -e DB_HOST=cyclos-db-test -e DB_NAME=cyclos -e DB_USER=%cyclos_user% -e DB_PASSWORD=%cyclos_password% cyclos/cyclos:4.8.2`

 * **Install Symfony test database**
    * `php bin/console doctrine:database:create --env=test`
    * `php bin/console doctrine:schema:update --env=test --force`
    * `php bin/console doctrine:database:import --env=test web/zipcities.sql`

 * **Inject into docker data that to be imported in Cyclos database**
    * `docker cp tests/test_members.csv cyclos-app-test:/usr/local/cyclos/`
    * `docker cp tests/test_simple_payments.csv cyclos-app-test:/usr/local/cyclos/`

 * **Restore the test database with the backup dump file**
    * `docker exec -u postgres -i cyclos-db-test psql --user %cyclos_user% cyclos < cyclos-dump.sql`

## Launch ##
 * `docker restart cyclos-db-test cyclos-app-test`
 * `./make-test.sh`
