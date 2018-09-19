#!/bin/sh

#WARNING : DO NOT RUN THIS IN A FOLDER WITH A GIT REPOSITORY

find . -type f -print0 | xargs -0 sed -i 's/machaine2/machaine2/g'


