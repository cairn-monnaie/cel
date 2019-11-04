#!/bin/sh

# $1 is the environment : dev / test


ROOT_DIR=$(pwd)

DATA_ONLY=0
for i in "$@"
do
    case $i in
        -e|--env)
            ENV="$2"
            shift #past argument
            shift #past value
            ;;
        -d|--data-only)
            DATA_ONLY=1
            shift
            ;;
        -h|--help)
            echo "Generates scripts to initialize docker services and containers with a dev/test dataset \n"
            echo "WARNING : This script should never be executed in a production context \n"
            echo "Usage:  --env ENV [--data-only] \n"
            echo "Options: \r"
            echo "    -e, --env          Set environment variable. It can be either dev/test. \r"
            echo "    -d, --data-only    Delete then generate dataset only instead of all services and docker containers. \r"
            echo "    -h, --help         Show this prompt with a list of options. \r"
            exit 0
            ;;

    esac
done

if [ "$ENV" = "dev" -o "$ENV" = "test" ]; then
    #analyse de la commande
    cd $ROOT_DIR/api

    #if config to generate
    if [ "$DATA_ONLY" = "0" ]; then
        if [ "$ENV" = "dev" ]; then
            OTHER_ENV="test"
        else
            OTHER_ENV="dev"
        fi

        if [ -f ./etc/cyclos/cyclos_constants_$ENV.yml ] || [ ! -f ./etc/cyclos/cyclos_constants_$OTHER_ENV.yml ]; then
            read -p "You are about to regenerate all the docker services. Are you sure? (y/n)" response

            if [ -z $response ]; then
                response="n"
                echo "To regenerate dataset only, use -d option"
            fi

            while [ $response != "y" ] && [ $response != "n" ]; do
                read -p "You are about to regenerate all the docker services. Are you sure? (y/n)" response
            done

            if [ $response = "n" ]; then
                exit 0
            fi

            echo "$(tput setaf 3) Delete cyclos data... $(tput sgr 0)"
            rm -rf data/cyclos
            rm etc/cyclos/cyclos_constants_$ENV.yml
            rm etc/cyclos/cyclos_constants_$OTHER_ENV.yml
            echo "$(tput setaf 2) Delete cyclos data... OK ! "

            sleep 2
            echo "$(tput setaf 3) Stop and remove containers, networks and volumes...  $(tput sgr 0)"
            docker-compose down
            echo "$(tput setaf 2)  Stop and remove containers, networks and volumes... OK !"

            sleep 2
            echo "$(tput setaf 3) (Re)create cyclos database container from cyclos-db service $(tput sgr 0)"
            docker-compose up -d cyclos-db
            sleep 10
            echo "$(tput setaf 2)  (Re)create cyclos database container from cyclos-db service... OK !"
            sleep 2

            echo "$(tput setaf 3) Restore cyclos database from dump file in $(pwd)/etc/cyclos/dump/cyclos.sql $(tput sgr 0)"
            sleep 3
            docker-compose exec -T cyclos-db psql -U cyclos cyclos < etc/cyclos/dump/cyclos.sql
            echo "$(tput setaf 2)  Restore cyclos database from dump file... OK !"
            sleep 2
            docker-compose up -d cyclos-app
        fi

        echo "$(tput setaf 3) Generate cyclos configuration and initial data... $(tput sgr 0)"
        docker-compose run --name api_container -e ENV=$ENV api sh etc/cyclos/setup_cyclos.sh
        docker container rm api_container
        docker-compose up -d api
        echo "$(tput setaf 2)  Generate cyclos configuration and initial data... OK !"
    else
        if [ -f ./etc/cyclos/cyclos_constants_$ENV.yml ]; then
            echo "$(tput setaf 3) Clean cyclos database from all users, payments and accounts data... $(tput sgr 0)"
            sleep 2
            docker-compose exec -T cyclos-db psql -v network="'%$ENV%'" -U cyclos cyclos < etc/cyclos/script_clean_database.sql
            echo "$(tput setaf 2) Clean cyclos database from all users, payments and accounts data... OK !"
            sleep 2
            echo "$(tput setaf 3) Regenerate cyclos init data : users, accounts credit and payments ... $(tput sgr 0)"
            sleep 2
            docker-compose exec -T  -e ENV=$ENV api python /cyclos/init_test_data.py http://cyclos-app:8080/ YWRtaW46YWRtaW4=
            echo "$(tput setaf 2) Regenerate cyclos init data : users, accounts credit and payments... OK !"
        else
            echo "Cyclos constants file not found for $ENV mode. The cyclos containers and configuration have not been settled. \n Remove -d option from the command"
            exit 0
        fi
    fi

    cd $ROOT_DIR/cel
    vendor=$(pwd)/vendor

    if [ ! -d "$vendor" ]; then
        RUN_COMPOSER=1
    else
        RUN_COMPOSER=0
    fi
    echo "$(tput setaf 3)(Re)create CEL services $(tput sgr 0)"
    docker-compose up -d
    echo "Wait 5 seconds to let services start..."
    sleep 5
    echo "$(tput setaf 2)(Re)create CEL services... OK !"
    sleep 2

    if [ "$RUN_COMPOSER" = "1" ]; then
        docker-compose exec engine composer install
        docker-compose exec engine assets:install
    fi

    echo "$(tput setaf 3)Generate mysql database schema and initial data based on cyclos database... $(tput sgr 0)"
    sleep 2
    docker-compose exec engine ./build-setup.sh $ENV
    docker-compose exec engine php bin/console cairn.user:generate-database --env=$ENV admin_network @@bbccdd
    echo "$(tput setaf 2)Generate mysql schema and initial data based on cyclos database... OK !"


    echo "$(tput setaf 2)The script ran successfully !"
    exit 0
else
    echo "choose dev / test as a script variable"
    exit 1
fi
