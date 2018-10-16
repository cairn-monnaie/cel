#!/bin/sh                                                                      

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

#docker run -d --name=cyclos-db-test --net=cyclos-net --hostname=cyclos-db-test -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclos cyclos/db
#docker run -d --name=cyclos-test-app -p 1235:8080 --net=cyclos-net -e DB_HOST=cyclos-db-test -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclos cyclos/cyclos                                                    
#
echo "$(tput setaf 3)Copy CSV file with members into docker container$(tput sgr 0)"
docker cp tests/test_members.csv cyclos-app-test:/usr/local/cyclos/            
echo "$(tput setaf 2)CSV file copied"
###docker cp tests/test_admins.csv cyclos-test-app:/usr/local/cyclos/            
echo "$(tput setaf 3)Copy CSV file with payments into docker container$(tput sgr 0)"
docker cp tests/test_simple_payments.csv cyclos-app-test:/usr/local/cyclos/    
echo "$(tput setaf 2)CSV file copied"
##
#docker restart cyclos-db-test cyclos-test-app                                    
#docker exec -u postgres -i cyclos-db-test psql --user cyclos cyclos < ./tests/cyclos-dump.sql
#
#docker restart cyclos-db-test cyclos-test-app                                    
#
#sleep 20                                                                      

echo "$(tput setaf 3)Start testing$(tput sgr 0)"
./vendor/bin/simple-phpunit                                   

#                                                                              
#sudo docker stop cyclos-db-test cyclos-test-app                                  
#sudo docker unpause cyclos-app cyclosdev 
