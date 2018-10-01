Digital Cairn
=======

# requirements
 * composer https://getcomposer.org/download/
 * git
 * docker
# Install
## Download Sources
 > git clone https://github.com/cairn-monnaie/CairnB2B.git
## Get a Cyclos license  
 * Register at cyclos license server here [https://license.cyclos.org/app/guest/register]
 * note login and password (noted $license_login and $license_password respectively)
 * more details here [https://license.cyclos.org/]
## Install Cyclos on a Debian based system 
 * use docker [https://hub.docker.com/r/cyclos/cyclos/]
 > sudo docker network create cyclos-net
 
 > sudo docker run -d --name=cyclos-db --net=cyclos-net --hostname=cyclos-db -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclospwd cyclos/db

 > docker run -d --name=cyclos-app -p 1234:8080 --net=cyclos-net -e DB_HOST=cyclos-db -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclospwd cyclos/cyclos


## Configure Cyclos instance
 From now on, some symbols will be used and need to be defined :

   * xxx : fill with whatever you want and will not be needed later on 
   * $variable : fill it with whatever you want, and will be reused later on

 1. Reach your cyclos instance at www.example.com:1234/ (the first time, it can take several minutes to start)
 2. Cyclos license server authentication

     * Login name : $license_login(provided while registering a cyclos license)
     * Password : $license_password(provided while registering a cyclos license)
     * Click next
 3. Basic configuration

     * Application name (name of your cyclos instance)
     * localization : xxx
     * Click next
 4. System administrator 

     These are the profile fields and credentials of the main administrator of the application, so fill them
     carefully.
     * Name : xxx
     * Login name : $admin_login
     * E-Mail : $admin_email
     * Password : $admin_password
     * Confirm password : $admin_password
     * Click finish
 5. Basic network information

     * Name : $network_name
     * Internal name : xxx
     * Description : xxx
     * Click next
 6. Localization

     * Customize localization : leave it unchecked if you want to use default ones (defined at step 3)
     * Click next
 7. Administrator 

     * create a network administrator : uncheck
     * click next
 8. Currency

     * Currency name : $currency_name
     * Currency symbol : xxx
     * Currency symbol : xxx
     * click next 
 9. System accounts

     * Unlimited account : check and $debit_account
     * System account : $system_account
     * Additional system account : uncheck
     * Click next
 10. User account 

     * User account : $user_account
     * Default negative balance limit : even if filled with 0, refill it with 0(sign "-" visible)
     * Initial credit : xxx
     * Click next
 11. Brokers 

     * Setup brokers : uncheck
     * Click next
 12. Profile fields 

     * Leave all fields unchecked
     * Click next 
 13. References 

     * Check "not used"
     * Click next 
 14. Records

     * Leave all fields unchecked
     * Click next 
 15. Message categories

     * Click next 
 16. Advertisments

     * Click Finish 
 17. Network details 

     * Enabled : make sure it is checked
     * Click "switch to this network"
 18. Configure the currency to suit the application
    .Access : System(top tab) / Account Configuration(bold in left menu) / Currencies
     * Click on the currency( created at step 8) 
     * Decimal places : 2
     * Enable transfer number : check
     * Transfer number identifier length : 8
     * WARNING : NO prefix/suffix !
     * Click save
 19. Configure permissions of network administrators' group
    .Access : System(top tab) / User configuration(bold in left menu) / Groups / Network Administrators 
     * Name : $network_group_admins (by default Network administrators)
     * Internal name : xxx
     * Click save
     * Click Permissions(top-right tab on group screen)
     * General / My profile fields : enabled/registration/visible/editable : check for full name / login name / email
     * General / Manage my channels access : check
     *System Accounts : 
        * system accounts : check all
        * system to system payments : check all
        * system to user payments : check all
        * system recurring payments : check view + cancel
        * system scheduled payments : check view + cancel + block + unblock + process installment + settle installment
     * User management / user registration : check
     * User management / login users via web services : check
     * User management / User channels access : select "manage"

     *User Accounts : 
         * access user accounts : check all
         * payments as user to user : check all
         * payments as user to system : check all
         * recurring payments : check view + cancel
         * scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
         * Accounts balance limits : select "manage"
     * Click save

 20. Configure a transfer type from an existing account type
    .Access : System(top tab) / Account configuration(bold in left menu) / Account types
     * Click on $user_account(defined in step 10)
     * Click on "Transfer types" (top-right tab on account type screen)
     * Click on the first transfer type (from $user_account to $debit_account)
     * Enabled : check
     * Channels : check main web + web services + Mobile app
     * Allow recurring payments : check
     * Allow scheduled payments : check
     * Max installments on scheduled payments : 1
     * Click save
 
 21. Repeat step 20 for all transfer types in $user_account account type
 22. Repeat step 20 and 21 for all account types ($debit_account / $system_account)

 23. Configure group of members
    .Access : System(top tab) / User configuration(bold in left menu) / Groups
     * Click on group "Users"(unique member group)
     * Enabled : check
     * Name : $network_group_members (by default Users)
     * Click save

 24. Configure the Product associated with user Account Type $user_account
    .Access : System(top tab) / User Configuration(bold in left menu) / Products
     * Click on the only product (Members)
     * Name : fill with $user_account name
     * Internal name : fille with $user_account internal name
     * Accounts / User account : must contain $user_account
     * Accounts / Default negative balance limit :  refill with 0 (sign "-" must be visible)
     * Accounts / system payments : check all
     * Accounts / user payments : check all
     * Accounts / recurring payments : check view + cancel
     * Accounts / scheduled payments : check  view + cancel + block + unblock + process installment + settle installment
     * Click save

 25. Check product's assignation to Member group
    .Access : System(top tab) / User configuration(bold in left menu) / Groups
     * click on  $network_group_members (defined in step 23)
     * click on Products (top-right tab of the group screen)
     * check that the created product appears in "Products assigned to Group" table (should be assigned by default)

 26. Configure Global Administration's channels 
    .Access : Switch to Global administration (top-side on the screen)
    .Access : System (top tab) / System Configurations(bold in left menu) / Configurations
     * Click on "Global default" configuration
     * Click on channels (top-right of configuration screen) 
     * Click on web services
     * Enabled : check
     * User access : select "Enforced enabled"
     * session timeout : xxx
 27. Change password type configuration
    .Access : System (top tab) / User Configuration(bold in left menu) / Password types
     * Click on login password
     * password length :  8 to 25 
 28. Configure group of global administrators
    .Access : System (top tab) / User Configuration(bold in left menu) / Groups
     * Click on "Global administrators"
     * Name : $global_group_admins 
     * Click save

 29. Save the configuration into a backup file
    As you experienced, configuring a Cyclos instance is really a long process, and there are much more functionalities that are not even dealt with in the scope of this application. For this reason, having a backup sql file with the configuration saved is really useful.
     * To do so, type the following command :
     > sudo docker exec -i -u postgres cyclos-db pg_dump cyclos > cyclos-dump.sql   

    This way, if you have another instance of Cyclos to configure, you may just restore it using this backup file (see here for details https://hub.docker.com/r/cyclos/cyclos/)

## Install symfony project
 > composer install

 * creer un utilisateur symfony dédié
 During this step, you will provide some global parameters that the application needs.
 Be careful, you will need data provided during cyclos installation steps
 > sudo php $PATH/composer.phar update
     * cyclos_group_pros: $network_group_members (step 23)
     * cyclos_group_network_admins: $network_group_admins (step 19) 
     * cyclos_group_global_admins: $global_group_admins (step 28)
     * cyclos_network_cairn: $network_name (step 5)
     * cyclos_currency_cairn: $currency_name (step 8)
     * cyclos_global_admin_username: $admin_login (step 4)
     * cyclos_global_admin_password: $admin_password (step 4)
     * cyclos_global_admin_email: $admin_email (step 4)
     * cyclos_root_prod_url: 'www.example.com:1234/'

 Create initial administrator
     *request url with URI "/install". This creates a user on Doctrine's side with profile fields of cyclos global administrator
     
 Access application with admin credentials via "login" URI.  

## Testing ##
