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

     These are the profile fields and credentials of the global administrator on Cyclos-side, so fill them
     carefully and keep them. This user will not be used in the application, but exists to do configuration that involves all networks. 
     This is the only user who will be able to access Cyclos via the channel "main web".
     * Name : xxx
     * Login name : **_$global-admin-login_**
     * E-Mail : **_$global-admin-email_**
     * Password : **_$global-admin-password_**
     * Confirm password : **_$global-admin-password_**
     * Click finish
 5. **Basic network information**

     * Name : **_$network-name_**
     * Internal name : **_$network-internal-name_**
     * Description : xxx
     * Click next
 6. **Localization**

     * Customize localization : leave it unchecked if you want to use default ones (defined at step 3)
     * Click next
 7. **Administrator** 

     These are the profile fields of the main administrator in the application (network administrator on Cyclos-side), so customize them 
     according to your own use. 
     The password won't be used later on as this user won't be able to access Cyclos via the channel "main web", but as a matter of 
     security, provide a secured password.
     * create a network administrator : check
     * Name : **_$network-admin-name_**
     * Login name : **_$network-admin-login_**
     * E-Mail : **_$network-admin-email_**
     * Password : **_$network-admin-password_**
     * Confirm password : **_$network-admin-password_**
     * Click next
 8. **Currency**

     * Currency name : **_$currency-name_**
     * Currency symbol : xxx
     * Currency symbol : xxx
     * click next 
 9. **System accounts**

     * Unlimited account : check and provide a name **_$debit-account_**
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
     * Click Save

 20. **Configure permissions of network administrators' group**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups / Network Administrators_
     * Name : **_$network-group-admins_** (by default Network administrators)
     * Internal name : xxx
     * Click save
     * Click Permissions(top-right tab on group screen)
     * General :
        * My profile fields : enabled/registration/visible/editable : check for full name / login name / email / address
        * My profile fields : enabled/visible : check for Account number 
        * Passwords : change / at registration : check for Login Password
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
        * Passwords : uncheck everything
        * user channels access : select "manage"
     * User Accounts : 
        * access user accounts : check all
        * payments as user to user : check all
        * payments as user to system : check all
        * recurring payments : check view + cancel
        * scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
        * Accounts balance limits : select "manage"
     * Click save

 21. **Configure an account type and its corresponding transfer types**

     _Access : System(top tab) / Account configuration(bold in left menu) / Account types_
     * Click on **_%user-account%_** (defined in step 10)
     * Internal name : **_$user-account-internal_** (e.g memberAccount)
     * Click save
     * Click on "Transfer types" (top-right tab on account type screen)
     * Click on the first transfer type (from **_%user-account%_** to **_%debit-account%_**)
     * internal name : xxx (compulsary) (e.g toDebit)
     * Enabled : check
     * Channels : uncheck main web + check web services 
     * User identification methods : Login name 
     * Allow recurring payments : check
     * Allow scheduled payments : check
     * Max installments on scheduled payments : 1
     * Click save
 
 22. **Repeat step 20 for all transfer types in _%user-account%_ account type**
 23. **Repeat step 20 and 21 for all account types (_%debit-account%_ / _%system-account%_)**

 24. **Configure group of members**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups_
     * Click on group "Users"(unique member group)
     * Enabled : check
     * Name : **_$network-group-members_** (by default Users)
     * Internal name : xxx
     * Click save

 25. **Configure the Product associated with user Account Type %_user-account%_**

     _Access : System(top tab) / User Configuration(bold in left menu) / Products
     * Click on the only product (Members)
     * Name : fill with **_%user-account%_** name
     * Internal name : fil with **_%user-account%_** internal name
     * General
        * My profile fields : enabled/registration/visible/editable : check for full name / login name / email 
        * Passwords : Change / at registration : check for Login password
     * Accounts 
        * User account : must contain **_%user-account%_**
        * Default negative balance limit :  refill with 0 (sign "-" must be visible)
        * system payments : check all
        * user payments : check all
        * recurring payments : check view + cancel
        * scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
     * Click save
 26. **Check product's assignation to Member group**

     _Access : System(top tab) / User configuration(bold in left menu) / Groups_
     * click on  **_%network-group-members%_** (defined in step 23)
     * click on Products (top-right tab of the group screen)
     * check that the created product appears in "Products assigned to Group" table (should be assigned by default)

 28. **Configure Global Administration's configuration** 

     _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_

     * Generate account numbers
        * Click on "Global default" configuration
        * Account number
           * Enable account number : check
           * Mask : xxx (e.g ## ### ###)
        * Click save

     * Configure main web's channel
        * Click on channels (top-right of configuration screen) 
        * Click on "main web"
        * User access : select "Enabled by default"
        * session timeout : xxx (e.g 2 mins)
        * Click save

     * Configure web services' channel
       * Click on channels (top-right of configuration screen) 
       * Click on "web services"
       * User access : select "Enforced enabled"
       * session timeout : xxx (e.g 2 mins)
       * Perform payments / User identification methods for performing payments : check Login name / Account number
       * Click save
 29. **Change usernames configuration**
        
      Here we remove Cyclos custom login validation so that we rely only on our application's validation. This way, we avoid the problem of dissociation between validation between the 3rd-party application  and Cyclos.
      _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_
      * Click on "Global default" configuration
      * Regular expression for login name
      * Login name length : leave boxes empty
      * Click save

 30. **Change password type configuration**

      For now, the application sets up password lengths straight in the source code, and is not customizable. If you want to change them, you will have to modify the source code in the UserValidation class (\Cairn\UserBundle\Validator\UserValidator) and use exactly the same values.

     _Access : System (top tab) / User Configuration(bold in left menu) / Password types_
     * Click on login password
     * password length :  8 to 25 
     * Disallow obvious password :  uncheck
     * Avoid repeated passwords : uncheck
     * Expires after : 100 years (we don't deal with password expiration)
     * Click save
 31. **Configure group of global administrators**

     _Access : System (top tab) / User Configuration(bold in left menu) / Groups_
     * Click on "Global administrators"
     * Name : **_$global-group-admins_** 
     * Click save

 32. **Save the configuration into a backup file**

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
     * cyclos_network_cairn: **_%network-internal-name%_** (step 5)
     * cyclos_currency_cairn: **_%currency-name%_** (step 8)
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

 * **Create a client for OAuth**
    `php bin/console fos:oauth-server:create-client`

 * **Create the installed administrator (step 7 )**
    Be careful with the sequence
    `php bin/console cairn.user:create-install-admin %network-admin-login% %network-admin-password% `

 * **Access application with admin credentials**
     * visit "example.com/login"
     * login with installed admin credientials **_%admin-login%_** and **_%admin-password%_** and start browsing !  

# Testing #
## Install ##
 * **Install docker images**
    * `docker run -d --name=cyclos-db-test --net=cyclos-net --hostname=cyclos-db-test -e POSTGRES_DB=cyclos -e POSTGRES_USER=%cyclos-user% -e POSTGRES_PASSWORD=%cyclos-password% cyclos/db`
    
    The tag '4.8.2' enforces docker to install this specific version. It is chosen because there exists a script to clean the database from users and transactions which works only on this version, pretty useful while testing.
    * `docker run -d --name=cyclos-app-test -p 1235:8080 --net=cyclos-net -e DB_HOST=cyclos-db-test -e DB_NAME=cyclos -e DB_USER=%cyclos-user% -e DB_PASSWORD=%cyclos-password% cyclos/cyclos:4.8.2`

 * **Install Symfony test database**
    * `php bin/console doctrine:database:create --env=test`
    * `php bin/console doctrine:schema:update --env=test --force`
    * `php bin/console doctrine:database:import --env=test web/zipcities.sql`

 * **Customize data that will imported in Cyclos database**
    At the testing setup, the cyclos database will be filled with users and payments defined in the tests/*.csv files. In order to add initial payments to some of the users(payments from debit account to member account), the transfer type must be added. It depends on data provided in steps 20-21-22.
    * Open csv file tests/test_simple_payments.csv
    * Fill "type" column with format: {debit account internal name}.{debit-member transfer type's internal name} (e.g debit.toUser)
    * save the file

 * **Inject into docker data that to be imported in Cyclos database**
    * `docker cp tests/test_members.csv cyclos-app-test:/usr/local/cyclos/`
    * `docker cp tests/test_simple_payments.csv cyclos-app-test:/usr/local/cyclos/`

 * **Restore the cyclos test database with the backup dump file**
    * `docker restart cyclos-db-test cyclos-app-test`
    * `docker exec -u postgres -i cyclos-db-test psql --user %cyclos_user% cyclos < cyclos-dump.sql`

 * **Change the cyclos test database port**
    The backup restoration permits to have the desired cyclos configuration. However, the database port is the same for testing and dev in conf. 
    * Reach your testing instance global administration at example.com:1235/global
        *log in with global administrators credentials : **_%admin-login%_** and **_%admin-password%_**

         _Access : System (top tab) / System Configurations(bold in left menu) / Configurations_
        * Click on "Global default" configuration
        * Main URL : replace 1234 by 1235
        * Click save 
    
## Launch ##
 * `docker restart cyclos-db-test cyclos-app-test`
 * `./make-test.sh`
