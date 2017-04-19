---
title: How to backup MySQL database from time to time
date: 2017-04-19 16:16:35
tags:
- mysql
- shell-script
- devops
---

To backup your database from time to time, this can be done by write a simple shell script, and setup a cron job to run it

```sh
#!/bin/bash

# config
backup_dir=/path/to/backup/folder
max_backup_files=5
db_host="127.0.0.1"
db_name="my_db"
db_user="root"
db_pass="secret"

if [ ! -d $backup_dir ]; then
    mkdir $backup_dir
fi

output_file=$backup_dir/`date +"%Y-%m-%d_%H-%M-%S.sql"`
# the tables data you want to exclude, but keep the structure
excluded_tables=(
ex_table1
ex_table2
ex_table3
)

ignored_tables_string_with_option=''
ignored_tables_string=''
for tbl in "${excluded_tables[@]}"
do :
   ignored_tables_string_with_option+=" --ignore-table=${db_name}.${tbl}"
   ignored_tables_string+=" ${tbl}"
done

# change this line if you use postgresql
mysqldump $db_name -u $db_user -h $db_host -p$db_pass ${ignored_tables_string_with_option} > $output_file
mysqldump $db_name -u $db_user -h $db_host -p$db_pass ${ignored_tables_string} --no-data >> $output_file

total_sql=`ls -l $backup_dir/*.sql | wc -l`

if [ $total_sql -gt $max_backup_files ]; then
    old_file=`ls $backup_dir/*.sql | sort | head -n 1`
    echo "Exceed $max_backup_files files, remove $old_file"
    rm -f $old_file
fi
```

Setup cronjob

```
$ crontab -e
```

add the following content, example to backup everyday 11pm

```
0 23 * * * /path/to/script.sh >/dev/null 2>&1
```
