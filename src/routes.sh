#!/bin/sh

#WARNING : DO NOT RUN THIS IN A FOLDER WITH A GIT REPOSITORY

find . -type f -print0 | xargs -0 sed -i 's/[-]?[0-9]{19,}/[-]?[0-9]{1,}/g'


