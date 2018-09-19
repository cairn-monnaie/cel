#!/bin/sh

sudo systemctl stop apache2
sudo php bin/console server:stop
sudo docker stop cyclos-test-app cyclos_test
sudo docker stop cyclos-app cyclos_dev

