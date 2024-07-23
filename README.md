# SIMPLE BACKUP CI CLICKHOUSE

## What is SIMPLE BACKUP CI CLICKHOUSE?
SIMPLE BACKUP CI CLICKHOUSE is application to backup MySQL/MariaDB database.
run on CLI using:

- CodeIgniter 4 framework.
- Clickhouse as parquet generator from MySQL.
- Python as worker executable.

## Installation & updates
- run `git clone https://github.com/MahlukEdanKurangGairah/simple-backup-ci4-clickhouse.git`
- `cd simple-backup-ci4-clickhouse`
- `./composer.phar install`
- then `composer update`

also install python and library on requirements.txt

- `cd app/Executable/sql2mysql' && pip install -r requirements.txt`
- `cd app/Executable/parquet2cockroach' && pip install -r requirements.txt`
- `cd app/Executable/parquet2mysql' && pip install -r requirements.txt`

## Setup
Configuration on app/Config/App.yaml. change mysql_conf (target to backup) and mysql_restore_conf (targt to restore)
Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> This application under heavy development, use as yu wise.
## Run
`php public/index.php`
