#!/bin/sh

sudo systemctl restart apache2
sudo php bin/console server:start
sudo docker start cyclos-app cyclos_dev

