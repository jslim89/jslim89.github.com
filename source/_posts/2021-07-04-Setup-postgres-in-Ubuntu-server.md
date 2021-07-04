---
title: Setup postgres in Ubuntu server
date: 2021-07-04 16:31:23
tags:
- postgres
- ubuntu
---

To install PostgreSQL in Ubuntu _(tested in 20.04)_

```
echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
apt update
apt -y install postgresql
```

The version installed here is v13

## First time setup

For the first time, we need to create a new user & a new database

Switch to postgres user

```
root@host:/root# su postgres

# connect to postgres server
postgres@host:/root$ psql
```

Then

```
postgres=# CREATE ROLE my_user LOGIN PASSWORD 'secret';
CREATE ROLE
postgres=# CREATE DATABASE my_database;
CREATE DATABASE
postgres=# GRANT CONNECT ON DATABASE my_database TO my_user;
GRANT
postgres=# GRANT USAGE ON SCHEMA public TO my_user;
GRANT
```

**Import data if necessary**

Then run

```
postgres=# \c my_database
You are now connected to database "my_database" as user "postgres".
my_database=# GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO my_user;
GRANT
my_database=# GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO my_user;
GRANT
```

Then back to root user. To enable public remote access

```
postgres@host:/root$ exit
root@host:/root# vim /etc/postgresql/13/main/postgresql.conf
```

Change **listen_addresses** value to `*`

```
listen_addresses = '*'
```

Then edit **pg_hba.conf**

```
root@host:/root# vim /etc/postgresql/13/main/pg_hba.conf
```

Change the line 

```
host    all             all             127.0.0.1/32            md5
```

to

```
host    all             all             0.0.0.0/0              md5
```

Then restart server

```
root@host:/root# service postgresql restart
```

## References:

- [Linux downloads (Ubuntu)](https://www.postgresql.org/download/linux/ubuntu/)
- [How to create a user with PSQL](https://chartio.com/learn/postgresql/create-a-user-with-psql/)
- [PostgreSQL/Postgres Create Database](https://www.guru99.com/postgresql-create-database.html)
- [Configure PostgreSQL to allow remote connection](https://www.bigbinary.com/blog/configure-postgresql-to-allow-remote-connection)
