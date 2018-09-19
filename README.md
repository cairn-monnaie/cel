Digital Cairn
=======

# requirement
 * composer https://getcomposer.org/download/
 * git
 * docker
# install
## Download Sources
 > git clone https://github.com/mazda91/CairnB2B.git
## Install symfony project
 > composer install
## Install Cyclos on a Debian based system
 * use docker (https://hub.docker.com/r/cyclos/cyclos/)
 > sudo docker network create cyclos-net
 
 > sudo docker run -d --name=cyclos-db --net=cyclos-net --hostname=cyclos-db -e POSTGRES_DB=cyclos -e POSTGRES_USER=cyclos -e POSTGRES_PASSWORD=cyclospwd cyclos/db

 > docker run -d --name=cyclos-app -p 1234:8080 --net=cyclos-net -e DB_HOST=cyclos-db -e DB_NAME=cyclos -e DB_USER=cyclos -e DB_PASSWORD=cyclospwd cyclos/cyclos

 > docker cp tests/test_users.csv cyclos-app:/usr/local/cyclos/
 > docker cp tests/test_simple_payments.csv cyclos-app:/usr/local/cyclos/
