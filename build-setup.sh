#!/bin/sh

# $1 is the environment : dev / test / prod
# $2 is cyclos admin  credentials : username:password


echo "Creating MySQL database"
php bin/console doctrine:database:drop --env=$1 --force
php bin/console doctrine:database:create --env=$1
php bin/console doctrine:migrations:diff --env=$1
php bin/console doctrine:migrations:migrate --env=$1
php bin/console doctrine:database:import --env=$1 web/zipcities.sql
echo " MySQL database created : OK ! "

echo " Generating administrator user"
php bin/console cairn.user:create-install-admin admin_network @@bbccdd --env=$1 

echo "Build script finished ! "


