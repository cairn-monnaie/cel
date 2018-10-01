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
 * follow step 1 to register at license server (remember login $login and password $password)
 * follow steps 2 and 3
## Install Cyclos on a Debian based system (step 4)
 * use docker [https://hub.docker.com/r/cyclos/cyclos/]
 > sudo docker network create cyclos-net
 
 > sudo docker run -d --name=cyclos-db --net=cyclos-net --hostname=cyclos-db -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclospwd cyclos/db

 > docker run -d --name=cyclos-app -p 1234:8080 --net=cyclos-net -e DB_HOST=cyclos-db -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclospwd cyclos/cyclos

 > docker cp tests/test_users.csv cyclos-app:/usr/local/cyclos/
 > docker cp tests/test_simple_payments.csv cyclos-app:/usr/local/cyclos/

## Configure Cyclos instance
 1. Reach your cyclos instance at $domain:1234/ (the first time, it can take several minutes to start)
 2. Cyclos license server authentication

     ok
 
## Install symfony project
 > composer install
 > sudo php $pathto/composer.phar update
    *cyclos_network_cairn : $network
    *cyclos_currency_cairn : $currency
    *cyclos_global_admin_username : $login
    *cyclos_global_admin_password : $password
    *cyclos_group_pros : $group_pros
    *cyclos_group_adhrents : $group_adherents

