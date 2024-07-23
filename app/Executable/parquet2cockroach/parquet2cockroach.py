#!/usr/bin/env python3
import sys
import os
import yaml
import polars as pl
from sqlalchemy import create_engine, Column, MetaData, Table
from sqlalchemy.types import Integer, Float, String, Boolean, DateTime
from sqlalchemy_utils import database_exists, create_database

def load_config(config_file):
    with open(config_file, 'r') as file:
        return yaml.safe_load(file)

def get_sqlalchemy_type(polars_dtype):
    type_map = {
        pl.Int8: Integer,
        pl.Int16: Integer,
        pl.Int32: Integer,
        pl.Int64: Integer,
        pl.UInt8: Integer,
        pl.UInt16: Integer,
        pl.UInt32: Integer,
        pl.UInt64: Integer,
        pl.Float32: Float,
        pl.Float64: Float,
        pl.Boolean: Boolean,
        pl.Utf8: String,
        pl.Date: DateTime,
        pl.Datetime: DateTime
    }
    return type_map.get(polars_dtype, String)

def create_table_from_parquet(engine, table_name, df):
    metadata = MetaData()
    columns = []

    for col_name, dtype in df.schema.items():
        sa_type = get_sqlalchemy_type(dtype)
        columns.append(Column(col_name, sa_type))

    table = Table(table_name, metadata, *columns)
    metadata.create_all(engine)
    print(f"Tabel '{table_name}' berhasil dibuat.")

def main(argv):
    if len(argv) != 3:
        print("Penggunaan: python parquet_to_cockroachdb.py <file_parquet> <config_file.yaml>")
        sys.exit(1)

    parquet_file, config_file = argv[1:]

    # Baca konfigurasi
    try:
        config = load_config(config_file)
        cockroach_config = config['cockroach_conf']
    except Exception as e:
        print(f"Error membaca file konfigurasi: {e}")
        sys.exit(1)

    # Baca file Parquet menggunakan Polars
    try:
        df = pl.read_parquet(parquet_file)
        
        # Gunakan nama file Parquet (tanpa ekstensi) sebagai nama tabel
        table_name = os.path.splitext(os.path.basename(parquet_file))[0]
        print(f"Menggunakan nama tabel: {table_name}")
    except Exception as e:
        print(f"Error membaca file Parquet: {e}")
        sys.exit(1)

    # Buat koneksi ke CockroachDB
    try:
        engine_url = f"cockroachdb://{cockroach_config['username']}:{cockroach_config['password']}@{cockroach_config['host']}:{cockroach_config['port']}/{cockroach_config['dbrestore']}"
        engine = create_engine(engine_url)
        
        with engine.connect() as connection:
            print(f"Terhubung ke database '{cockroach_config['dbrestore']}'.")
    except Exception as e:
        print(f"Error saat menyiapkan koneksi CockroachDB: {e}")
        sys.exit(1)

    # Buat struktur tabel
    try:
        create_table_from_parquet(engine, table_name, df)
    except Exception as e:
        print(f"Error saat membuat struktur tabel: {e}")
        sys.exit(1)

    # Insert data ke CockroachDB
    try:
        # Konversi Polars DataFrame ke Pandas DataFrame untuk kompatibilitas dengan to_sql
        pandas_df = df.to_pandas()
        pandas_df.to_sql(table_name, con=engine, if_exists='append', index=False)
        print(f"Data berhasil diinsert ke tabel {table_name}")
    except Exception as e:
        print(f"Error saat memasukkan data ke CockroachDB: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main(sys.argv)