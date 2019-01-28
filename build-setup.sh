#!/bin/sh

# $1 is the environment : dev / test / prod
# $2 is cyclos admin  credentials : username:password

echo "Setting up Cyclos configuration"            
until [ `curl --silent --write-out '%{response_code}' -o /dev/null http://cyclos-app:8080/global/` -eq 200 ];
do                                                                             
    echo '--- waiting for Cyclos to be fully up (10 seconds)'                    
    sleep 10                                                                     
done

python3.5 setup_cyclos.py $1 `echo -n $2 | base64 `
echo "Cyclos configuration setup : OK ! "

echo "Creating MySQL database"
php bin/console doctrine:database:drop --env=$1 --force
php bin/console doctrine:database:create --env=$1
php bin/console doctrine:schema:create --env=$1
php bin/console doctrine:database:import --env=$1 web/zipcities.sql
echo " MySQL database created : OK ! "

echo " Generating administrator user"
php bin/console cairn.user:create-install-admin admin_network @@bbccdd --env=$1 

echo "Build script finished ! "


