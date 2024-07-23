#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error
import sys
import yaml

def load_config(config_path):
    with open(config_path, 'r') as file:
        config = yaml.safe_load(file)
    return config

def execute_sql_file(file_path, db_config):
    try:
        # Membuat koneksi ke database MySQL
        connection = mysql.connector.connect(
            host=db_config['host'],
            user=db_config['username'],
            password=db_config['password'],
            database=db_config['dbname'],
            port=db_config['port']
        )

        if connection.is_connected():
            cursor = connection.cursor()

            # Membaca isi file SQL
            with open(file_path, 'r') as sql_file:
                sql_script = sql_file.read()

            # Mengeksekusi skrip SQL
            for result in cursor.execute(sql_script, multi=True):
                if result.with_rows:
                    # print(f"Rows produced by statement '{result.statement}':")
                    # print(result.fetchall())
                    print(f"Data Berhasil Insert: {result.rowcount}")
                else:
                    # print(f"Number of rows affected by statement '{result.statement}': {result.rowcount}")
                    print(f"Number of rows affected by statement: {result.rowcount}")

            # Commit perubahan
            connection.commit()
            print("File SQL berhasil dieksekusi.")

    except Error as e:
        print(f"Error: {e}")

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("Koneksi ke MySQL ditutup.")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python script.py <config_path> <file_path>")
    else:
        config_path = sys.argv[1]
        file_path = sys.argv[2]

        # Memuat konfigurasi dari file YAML
        config = load_config(config_path)

        # Mengeksekusi file SQL dengan konfigurasi database
        execute_sql_file(file_path, config['mysql_restore_conf'])