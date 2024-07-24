# SIMPLE BACKUP CI CLICKHOUSE

## What is SIMPLE BACKUP CI CLICKHOUSE?
SIMPLE BACKUP CI CLICKHOUSE is application to backup MySQL/MariaDB database, run on linux.
run on CLI using:

- CodeIgniter 4 framework.
- Clickhouse converter from MySQL table to parquet.
- Python as worker executable.

## Installation & updates
- run `git clone https://github.com/MahlukEdanKurangGairah/simple-backup-ci4-clickhouse.git`
- `cd simple-backup-ci4-clickhouse`
- `./composer.phar install` then `./composer.phar update`

download clickhouse and place it on Executable folder

`cd app/Executable && curl https://clickhouse.com/ | sh`

install python and library on requirements.txt

- `cd app/Executable/sql2mysql' && pip install -r requirements.txt`
- `cd app/Executable/parquet2cockroach' && pip install -r requirements.txt`
- `cd app/Executable/parquet2mysql' && pip install -r requirements.txt`

## Setup
Configuration on `app/Config/App.yaml`. change `mysql_conf` (target to backup) and `mysql_restore_conf` (target to restore)

configure username and password mysql for target backup and target restore
- `app/Config/client_backup.cnf`
- `app/Config/client_restore.cnf`

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

## Run
`/usr/bin/php public/index.php`

backup folder will placed in `backup_folder` with 2 folder 'struktur' and 'parquet'

> [!WARNING]
> This application under heavy development, use as yu wise.
