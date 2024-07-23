# SIMPLE BACKUP CI CLICKHOUSE

## What is SIMPLE BACKUP CI CLICKHOUSE?
SIMPLE BACKUP CI CLICKHOUSE is application to backup MySQL/MariaDB database.
run on CLI using:
> CodeIgniter 4 framework
> Clickhouse as parquet generator from MySQL
> Python as worker executable

## Installation & updates
`git clone https://github.com/MahlukEdanKurangGairah/simple-backup-ci4-clickhouse.git`
`./composer.phar install` then `composer update`
also install python and library on requirements.txt
`cd APPPATH.'Executable/sql2mysql' && pip install -r requirements.txt`

## Setup
Configuration on app/Config/App.yaml. change mysql_conf (target to backup) and mysql_restore_conf (targt to restore)
Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> This application under heavy development, use as yu wise.
> The end of life date for PHP 8.0 was November 26, 2023.
> If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> The end of life date for PHP 8.1 will be November 25, 2024.
