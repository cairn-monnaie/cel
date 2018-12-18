<?php

//Cairn/tests/bootstrap.php


//boostrap work : generate users if not already set
if(isset($_ENV['BOOTSTRAP_GEN_DB_ENV']) && $_ENV['BOOTSTRAP_GEN_DB_ENV'] == 'test'){
    passthru(sprintf(
        'php "%s/../bin/console" cairn.user:create-install-admin --env=test admin_network @@bbccdd',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" cairn.user:generate-database --env=%s admin_network @@bbccdd',
        __DIR__,
        $_ENV['BOOTSTRAP_GEN_DB_ENV']
    ));

}

require __DIR__.'/../vendor/autoload.php';

