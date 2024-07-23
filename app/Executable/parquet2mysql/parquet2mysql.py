#!/usr/bin/env python3
import sys
import os
import yaml
import polars as pl
from sqlalchemy import create_engine, Column, MetaData, Table
from sqlalchemy.dialects.mysql import INTEGER, FLOAT, VARCHAR, BOOLEAN, DATETIME, LONGTEXT
from sqlalchemy_utils import database_exists, create_database

def load_config(config_file):
    with open(config_file, 'r') as file:
        return yaml.safe_load(file)

def ensure_database_exists(engine, database_name):
    if not database_exists(engine.url):
        create_database(engine.url)
        print(f"Database '{database_name}' berhasil dibuat.")
    else:
        print(f"Database '{database_name}' sudah ada.")

def main(argv):
    if len(argv) != 3:
        print("Penggunaan: python parquet2mariadb.py <file_parquet> <config_file.yaml>")
        sys.exit(1)

    parquet_file, config_file = argv[1:]

    # Baca konfigurasi
    try:
        config = load_config(config_file)
        mariadb_config = config['mysql_restore_conf']
        # print(f"{mariadb_config}")
    except Exception as e:
        print(f"Error membaca file konfigurasi: {e}")
        sys.exit(1)

    # Baca file Parquet menggunakan Polars
    try:
        df = pl.read_parquet(parquet_file)
        """ columns_to_convert = [col for col in df.columns if 'created_at' in col]
        for col in columns_to_convert:
            try:
                df = df.with_columns([
                    pl.col(col).cast(pl.Datetime)
                ])
                print(f"Kolom '{col}' berhasil dikonversi ke datetime")
            except Exception as e:
                print(f"Error saat mengonversi kolom '{col}': {e}") """
                
        
        # Gunakan nama file Parquet (tanpa ekstensi) sebagai nama tabel
        table_name = os.path.splitext(os.path.basename(parquet_file))[0]
        print(f"Menggunakan nama tabel: {table_name}")
    except Exception as e:
        print(f"Error membaca file Parquet: {e}")
        sys.exit(1)

    # Buat koneksi ke MariaDB dan pastikan database ada
    try:
        connection_url = f"mysql+pymysql://{mariadb_config['username']}:{mariadb_config['password']}@{mariadb_config['host']}:{mariadb_config['port']}/{mariadb_config['dbname']}"
        engine = create_engine(connection_url)
        ensure_database_exists(engine, mariadb_config['dbname'])
        print(f"Terhubung ke database '{mariadb_config['dbname']}'.")
    except Exception as e:
        print(f"Error saat menyiapkan koneksi MariaDB: {e}")
        sys.exit(1)

    # Insert data ke MariaDB
    try:
        # Konversi Polars DataFrame ke Pandas DataFrame untuk kompatibilitas dengan to_sql
        pandas_df = df.to_pandas()
        pandas_df.to_sql(table_name, con=engine, if_exists='append', index=False)
        print(f"Data berhasil diinsert ke tabel {table_name}")
    except Exception as e:
        print(f"Error saat memasukkan data ke MariaDB: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main(sys.argv)