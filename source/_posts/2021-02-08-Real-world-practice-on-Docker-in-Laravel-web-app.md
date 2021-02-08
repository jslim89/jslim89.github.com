---
title: Real world practice on Docker in Laravel web app
date: 2021-02-08 08:29:31
tags:
- docker
- laravel
---

I heard about Docker few years ago, but wasn't know about best practice how to use that.
I've tried Laradock, it's way too many stuff in the config, should I use 1 docker for multiple
projects? Or better single project?

----

Create **docker-compose.yml** in root

Here's the basic docker-compose file to run php web app

```
.
├── docker-compose.yml
└── src (laravel project)
```

```yml
version: '3'

services:
  php-fpm:
    image: php:8.0-fpm
    container_name: jslim-php-fpm
    restart: unless-stopped
    tty: true
    working_dir: /var/www/app
    depends_on:
      - redis
    volumes:
      - ../src:/var/www/app
    networks:
      - jslim-network

  nginx:
    image: nginx:alpine
    container_name: jslim-nginx
    restart: unless-stopped
    tty: true
    depends_on:
      - php-fpm
    ports:
      - 8080:80
    volumes:
      - ../src:/var/www/app
    networks:
      - jslim-network

  redis:
    image: redis:6-alpine
    container_name: jslim-redis
    networks:
      - jslim-network


#Docker Networks
networks:
  jslim-network:
    driver: bridge
```

If you're in local development, can add in **mysql** container.

```
  php-fpm:
    ...
    depends_on:
      - mysql
  
  ...
  
  mysql:
    image: mysql:8.0
    container_name: jslim-mysql
    ports:
      - 3306:3306
    environment:
      MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
      MYSQL_DATABASE: '${DB_DATABASE}'
      MYSQL_USER: '${DB_USERNAME}'
      MYSQL_PASSWORD: '${DB_PASSWORD}'
      MYSQL_ALLOW_EMPTY_PASSWORD: 'yes'
    volumes:
      - ./data/mysql-data:/var/lib/mysql
    networks:
      - jslim-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping"]
```

Run it  
_(`. src/.env` is read .env file's as env variables)_

```
$ . src/.env && docker-compose up -d
```

## Add extra php packages

In order to run Laravel web app, there are some php extensions required.
Thus, we need to create a **Dockerfile** to customize from the base image _(php:8.0-fpm)_

```
.
├── docker
│   └── php
│       └── Dockerfile
├── docker-compose.yml
└── src (laravel project)
```

Update **Dockerfile**

```
FROM php:8.0-fpm

ARG DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    procps \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# install npm
RUN curl -sL https://deb.nodesource.com/setup_15.x | bash -
RUN apt-get install -y nodejs
RUN curl -L https://npmjs.org/install.sh | sh

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN pecl install redis && docker-php-ext-enable redis

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000
CMD ["php-fpm"]
```

And update **docker-compose.yml**

add

```yml
services:
  php-fpm:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
```

remove

```
image: php:8.0-fpm
```

Remember to update **.env**, to point the DB & redis host to docker container

```
DB_HOST=mysql
REDIS_HOST=redis
```

Now run `docker-compose up -d` should be able to visit `127.0.0.1:8080`

----

## Create customized base image

Can build the image to [Docker Hub](https://hub.docker.com/), it's free for public image

[Refer to my Github repo](https://github.com/jslim89/docker-config-dev/tree/master/base-images/php-fpm)
for how to create base image. It's based on `php:8.0-fpm` image

![Docker image concept](/images/posts/2021-02-08-Real-world-practice-on-Docker-in-Laravel-web-app/docker-image-concept.png)

Basically the idea is, if we have 2nd or 3rd Laravel project, can just use the
base image, and duplicate the required extensions for all projects.
It also take quite a few minutes to install everything when we run `docker-compose up`.
Base image is like a template, everything is ready, can use in all Laravel projects.
Just extends the pre-built custom base image _(i.e. jslim/php8-laravel-base in my example here)_

Update **Dockerfile**, change

```
FROM php:8.0-fpm
```

to

```
FROM jslim/php8-laravel-base:latest
```

and remove those apt install commands

----

## In production

In production we usually don't use database in same instance with web app,
we can create another **docker-compose-production.yml**

Remove **mysql**, and perhaps need to have custom **php.ini** config,
and may be have custom nginx config, SSL cert, etc

```yml
version: '3'

services:
  php-fpm:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
    container_name: jslim-php-fpm
    restart: unless-stopped
    tty: true
    working_dir: /var/www/app
    depends_on:
      - redis
    volumes:
      - ../src:/var/www/app
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - jslim-network

  nginx:
    image: nginx:alpine
    container_name: jslim-nginx
    restart: unless-stopped
    tty: true
    depends_on:
      - php-fpm
    ports:
      - 80:80
      - 443:443
    volumes:
      - ../src:/var/www/app
      - ./docker/nginx/conf.d/nginx.conf:/etc/nginx/conf.d/app.conf
      - ./docker/nginx/ssl-snippets.conf:/etc/nginx/snippets/ssl.conf
      - ./docker/nginx/ssl/nginx.crt:/etc/ssl/certs/nginx.crt
      - ./docker/nginx/ssl/nginx.key:/etc/ssl/private/nginx.key
    networks:
      - jslim-network

  redis:
    image: redis:6-alpine
    container_name: jslim-redis
    networks:
      - jslim-network

#Docker Networks
networks:
  jslim-network:
    driver: bridge
```

The file structure

```
├── docker
│   ├── nginx
│   │   ├── conf.d
│   │   │   └── nginx.conf
│   │   ├── ssl
│   │   │   ├── nginx.crt
│   │   │   └── nginx.key
│   │   └── ssl-snippets.conf
│   └── php
│       ├── Dockerfile
│       └── local.ini
├── docker-compose.yml
├── docker-compose-production.yml
└── src (laravel project)
```

To run in production

```
$ docker-compose -f docker-compose-production.yml up -d
```
