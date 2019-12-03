# Installation guide

## Requirements
 * docker && docker-compose
 * git
 * Install [api repo](https://github.com/cairn-monnaie/api/tree/cairn)

## Download Sources
  Download the sources such that both the repositories _api_ & _cel_ are set up this way :
 ```
numerique (parent dir)
│   setup_env.sh
└─── api
│   │   Dockerfile
│   │   docker-compose.yml
│   │   .env
│   │   ...
└─── cel
│    │   Dockerfile
│   │   docker-compose.yml
│   │   .env
│   │   ...
```
   `git clone https://github.com/cairn-monnaie/cel.git`

## Docker initial setup
 * **Set up global parameters**

    Copy the template file containing environment variables and open it with your favorite editor.   
      ```
        cd $pathToParentDir/cel  
        cp .env.dist .env
      ```

    Set the different variables and ports, ensuring that ports are not already in use by another application.
    To make sure of it, you can use the following command :  
      `sudo lsof -i :xxx` xxx being the port
 
    Copy the template file containing symfony app global variables and open it with your favorite editor.  
      `cp app/config/parameters.yml.dist app/config/parameters.yml`

    Some of these parameters rely directly on this `docker-compose.yml` file and the [api repo](https://github.com/cairn-monnaie/api/tree/cairn) `docker_compose.yml` :  
      `database_host: db (name of the docker service containing MySQL database)`  
      `database_port: 3306 (port listener in the db container)`  
      `database_name: db-name`  
      `database_user: username (provided in .env)`  
      `database_password: pwd (provided in .env)`  
      `mailer_host: engine (name of the docker service executing php code)`  
      `cyclos_root_prod_url: 'http://cyclos-app:8080/' (name and port of the cyclos-app docker service)`  
      `cyclos_group_pros: xxx (name of the group of professionals in your cyclos application)`  
      `cyclos_group_network_admins: 'Network administrators'`  
      `cyclos_group_global_admins: 'Global administrators'`  
      `cyclos_currency_cairn: '<currency>' (name of services.api.environment.CURRENCY_SLUG  in docker-compose of api repo)`
      `router.request_context.host: 'cyclos-app:8080'`
      `router.request_context.scheme: https`
      `router.request_context.base_url: null`

    Some of these parameters are always customizable according to your use :
      `mailer_transport: smtp`  
      `mailer_user: xxx (e.g admin@localhost.fr)`  
      `mailer_port: xxx`  
      `mailer_password: xxx`  
      `secret: ThisTokenIsNotSoSecretChangeIt`  
      `cairn_email_noreply: xxx (e.g noreply@localhost.fr)`  
      `cairn_card_rows: xxx (e.g 5)`  
      `cairn_card_cols: xxx (e.g 5)`  
      `cairn_email_technical_services: xxx (e.g services@localhost.fr)`  
      `card_activation_delay: xxx (e.g 10)`  
      `cairn_default_conversion_description: xxx (e.g 'Conversion euros-cairns')`  
      `cairn_default_withdrawal_description: xxx (e.g 'Withdrawal cairns')`  
      `cairn_default_deposit_description: xxx  (e.g 'Deposit cairns')`  
      `cairn_default_reconversion_description: xxx (e.g 'Reconversion cairns-euros')`  
      `cairn_default_transaction_description: xxx (e.g 'Virement Cairn')`  
      `cairn_email_activation_delay: xxx (e.g 10)`  
      `leading_company: 'Association ...'`
      `cairn_default_register_url : xxx (e.g 'https://domain.com/register')`
      `cairn_default_cgu_url : xxx (e.g 'https://domain.com/register')`

 * **Setup the application**
     * Build docker images  (if necessary)
       `sudo docker-compose build`

     * Launch the main script(from parent dir) to install services  
       You may need first to give `setup_env.sh` the executable permissions.  
       This script should not be executed in a production environment in order to avoid data loss. Each command should be ran one by one in order to make sure you know what you are doing. 
       ```
       cp setup_env.sh ..
       cd $pathToParentDir/
       sudo ./setup_env.sh --env $env                               #$env = (test/dev)
       ``` 
     
    * Install assets
       `sudo docker-compose exec engine assets:install`

     * Enable engine's user to write logs, cache files and web static files(images)  
       `sudo docker-compose exec engine chown -R www-data:www-data var web`

## Questions
 * **How to clear the cache**
   ```
     cd $pathToParentDir/cel
     sudo rm -rf var/cache/*
     sudo docker-compose exec engine php bin/console cache:clear --env=$env
     sudo docker-compose exec engine chown -R www-data:www-data var web
   ```  
 * **How to regenerate data & cyclos config**  
   Go to the parent directory
   ```
     cd $pathToParentDir/
     sudo ./setup_env.sh --env $env                                #$env = (test/dev)
   ```
 * **How to regenerate data only**  
   Go to the parent directory
   ```
     cd $pathToParentDir/
     sudo ./setup_env.sh --env $env -d                              #$env = (test/dev)
   ```

## Troubleshooting
 * **Composer update : How to solve "Allowed memory size of xxx exhausted (tried to allocate 43148176 bytes) in php"**
   * In dev/test environment : 
     * open the php configuration file in your favorite text editor
       ```
       cd $pathToParentDir/cel
       vim docker/engine/php.ini
       ```
     * Set the memory limit a script can consume to infinity
       `memory_limit = -1`
     * Restart the container
       ```
        sudo docker-compose restart engine
        sudo docker-compose exec engine composer update
       ```
       
   * In production environment
     DO NOT use `composer update` in a production environment. This would result in a possibly new state of your libraries you never tested against. Use :
     `composer install`  
   
    For more information, see [Composer documentation](https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors)
 * **Composer install/update : How to solve an error with "proc_open() failed" message**
   See [Composer documentation](https://getcomposer.org/doc/articles/troubleshooting.md#proc-open-fork-failed-errors)


