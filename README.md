Digital Cairn
=======

# requirement
 * composer https://getcomposer.org/download/
 * git
 * docker
# install
## Download Sources
 > git clone https://github.com/mazda91/CairnB2B.git
## Get a Cyclos license 
 * access Cyclos license server [https://license.cyclos.org/]
 * follow step 1 to register at license server (remember login $license_login and password $license_password)
 * follow steps 2 and 3
## Install Cyclos on a Debian based system (step 4)
 * use docker [https://hub.docker.com/r/cyclos/cyclos/]
 > sudo docker network create cyclos-net
 
 > sudo docker run -d --name=cyclos-db --net=cyclos-net --hostname=cyclos-db -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclospwd cyclos/db

 > docker run -d --name=cyclos-app -p 1234:8080 --net=cyclos-net -e DB_HOST=cyclos-db -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclospwd cyclos/cyclos

 > docker cp tests/test_users.csv cyclos-app:/usr/local/cyclos/
 > docker cp tests/test_simple_payments.csv cyclos-app:/usr/local/cyclos/

## Configure Cyclos instance
 From now on, some symbols will be used and need to be defined :

   * xxx : fill with whatever you want, does not really matter for compatibilty with symfony
   * $variable : fill it with whatever you want, and will be reused later on

 1. Reach your cyclos instance at $domain:1234/ (the first time, it can take several minutes to start)
 2. Cyclos license server authentication

     * Login name : $license_login
     * Password : $license_password
     * Click next
 3. Basic configuration

     * Application name (name of your cyclos instance)
     * ...
     * Click next
 4. System administrator
     These are the profile fields and credentials of the main administrator of the application (on both Cyclos/Symfony sides), so fill them
     carefully.
     * Name : xxx
     * Login name : $admin_login
     * E-Mail : xxx
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

     * Unlimited account : check and xxx
     * System account : xxx
     * Additional system account : uncheck
     * Click next
 10. User account

     * User account : xxx
     * Default negative balance limit : even if filled with 0, refill it with 0(sign "-" visible)
     * Initial credit : xxx
     * Click next
 11.  Brokers

     * Setup brokers : uncheck
     * Click next
 12. Profile fields

     * leave all fields unchecked
     * Click next 
 13.  References 

     * check "not used"
     * Click next 

 14.  Records

     * leave all fields unchecked
     * Click next 

 15. Message categories
     * Click next 

12) Advertisments
    *click next 

 
## Install symfony project
 > composer install
 > sudo php $pathto/composer.phar update
    *cyclos_network_cairn : $network
    *cyclos_currency_cairn : $currency
    *cyclos_global_admin_username : $login
    *cyclos_global_admin_password : $password
    *cyclos_group_pros : $group_pros
    *cyclos_group_adhrents : $group_adherents

