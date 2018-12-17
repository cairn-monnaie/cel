#!/bin/sh                                                                      
cp app/config/parameters_local.yml app/config/parameters.yml
echo "$(tput setaf 3) Start dropping Symfony previous database $(tput sgr 0)"
php bin/console doctrine:database:drop --env=test --force                      

echo "$(tput setaf 3) Clean Cyclos database from previous users and payments $(tput sgr 0)"
sleep 5
sudo docker exec -i -u postgres cyclos-db-test psql --user cyclos cyclos < tests/script_clean_database.sql
echo "$(tput setaf 2) Cyclos database cleaned ! "

echo "$(tput setaf 3)Start creating new Symfony database"$(tput sgr 0)
php bin/console doctrine:database:create --env=test                            
php bin/console doctrine:schema:update --env=test --force                      
php bin/console doctrine:database:import --env=test web/zipcities.sql          

echo "$(tput setaf 3)Start testing$(tput sgr 0)"
#./vendor/bin/simple-phpunit --testsuite=Order --stop-on-error
