#!/bin/bash

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

            rm -rf data/cyclos
            rm etc/cyclos/cyclos_constants_$ENV.yml
            rm etc/cyclos/cyclos_constants_$OTHER_ENV.yml

            docker-compose down
            docker-compose up -d cyclos-db
            sleep 10
            docker-compose exec -T cyclos-db psql -U cyclos cyclos < etc/cyclos/dump/cyclos.sql
            docker-compose up -d cyclos-app
        fi

        docker-compose run --name api_container -e ENV=$ENV api sh etc/cyclos/setup_cyclos.sh
        docker container rm api_container
        docker-compose up -d api
    else
        if [ -f ./etc/cyclos/cyclos_constants_$ENV.yml ]; then
            docker-compose exec -T cyclos-db psql -v network="'%$ENV%'" -U cyclos cyclos < etc/cyclos/script_clean_database.sql
            docker-compose exec -T  -e ENV=$ENV api python /cyclos/init_test_data.py http://cyclos-app:8080/ YWRtaW46YWRtaW4=
        else
            echo "Cyclos constants file not found for $ENV mode. The cyclos containers and configuration have not been settled. \n Remove -d option from the command"
            exit 0
        fi
    fi

    cd $ROOT_DIR/cel
    docker-compose up -d
    sleep 10
    docker-compose exec engine ./build-setup.sh $ENV
    docker-compose exec engine php bin/console cairn.user:generate-database --env=$ENV admin_network @@bbccdd
    exit 0
else
    echo "choose dev / test as a script variable"
    exit 1
fi
