#!/bin/sh

docker pause cyclos-app cyclos_dev

php bin/console doctrine:database:drop --env=test --force
docker rm -fv cyclos_test cyclos-test-app

php bin/console doctrine:database:create --env=test 
php bin/console doctrine:schema:update --env=test --force 
php bin/console doctrine:database:import --env=test web/zipcities.sql

docker run -d --name=cyclos_test --net=cyclos-net --hostname=cyclos_test -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclos cyclos/db
docker run -d --name=cyclos-test-app -p 1235:8080 --net=cyclos-net -e DB_HOST=cyclos_test -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclos cyclos/cyclos

docker cp tests/test_members.csv cyclos-test-app:/usr/local/cyclos/
#docker cp tests/test_admins.csv cyclos-test-app:/usr/local/cyclos/
docker cp tests/test_simple_payments.csv cyclos-test-app:/usr/local/cyclos/

docker restart cyclos_test cyclos-test-app

sleep 20
docker exec -u postgres -i cyclos_test psql --user cyclos cyclos < ./tests/cyclos_config.sql

docker restart cyclos_test cyclos-test-app

sleep 20
./vendor/bin/phpunit

#sudo docker stop cyclos_test cyclos-test-app  
#sudo docker unpause cyclos-app cyclos_dev

