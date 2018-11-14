#!/bin/sh                                                                      

hour=$(date +%X)
date=$(date +%x)

PATH_ACCOUNT_INFORMATION=tests/performances/AccountsInformation
PATH_LOGIN=tests/performances/Login

echo "$(tput setaf 3) Start load test for accounts consulting scenario$(tput sgr 0)"
jmeter -n -l $PATH_ACCOUNT_INFORMATION/$date.$hour.csv -t $PATH_ACCOUNT_INFORMATION/load_test_by_steps.jmx -JnombreUnites-TG1=50 -JdureeMontee-TG1=50 -JnombreUnites-TG2=60 -JdureeMontee-TG2=50
echo "$(tput setaf 2) Load testing is over ! "
sleep 5

echo "$(tput setaf 3) Start load test for login scenario$(tput sgr 0)"
jmeter -n -l $PATH_LOGIN/$date.$hour.csv -t $PATH_LOGIN/load_test_by_steps.jmx -JnombreUnites-TG1=50 -JdureeMontee-TG1=50 -JnombreUnites-TG2=60 -JdureeMontee-TG2=50
echo "$(tput setaf 2) Load testing is over ! "

