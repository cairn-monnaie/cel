FROM debian:9.6

LABEL description="CairnB2B based on debian" \
        maintainer="mazda91 <https://github.com/mazda91>"

RUN apt-get update 
RUN apt-get install -y docker
RUN apt-get install -y python3-pip
RUN pip3 install python-slugify PyYAML datetime requests 
RUN apt-get install -y curl
   
COPY . /var/www/Symfony

WORKDIR /var/www/Symfony

#RUN ["ln", "-s","./docker/front/moncompte.conf", "./"]

RUN ["chmod", "+x", "./build-cyclos.sh"]

ENTRYPOINT ["./build-cyclos.sh"]
