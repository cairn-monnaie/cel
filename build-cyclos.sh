#!/bin/sh

#echo "$(tput setaf 3) Setting up Cyclos configuration"$(tput sgr 0)            
until [ `curl --silent --write-out '%{response_code}' -o /dev/null http://cyclos-app:8080/global/` -eq 200 ];
do                                                                             
    echo '--- waiting for Cyclos to be fully up (10 seconds)'                    
    sleep 10                                                                     
done
#
python3.5 setup_cairn_app.py dev  `echo -n admin:admin | base64`                   
#echo "$(tput setaf 2) Cyclos configuration setup : OK ! "
