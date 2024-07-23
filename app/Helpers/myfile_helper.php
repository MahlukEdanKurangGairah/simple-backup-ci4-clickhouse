<?php
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\StorageAttributes;

if(!function_exists('folder_exist')){
    function folder_exist($backupFolder){
        $base_dir = '/var/www/html/backup_folder';
        $adapter = new LocalFilesystemAdapter($base_dir);
        $filesystem = new Filesystem($adapter);
        $arIsi = $filesystem->listContents($backupFolder)
            ->filter(function(StorageAttributes $attributes){
                return $attributes->isDir();
            })->map(function(StorageAttributes $attributes){
                return $attributes->path();
            })->toArray();
        return $arIsi;
        if (count($arIsi)<1) return true;
        elseif(in_array($folder,$arIsi)) return true;
        return false;
    }
}

if(!function_exists('backup_list_folder')){
    function backup_list_folder($folder){
        $arFlyConf = PortableVisibilityConverter::fromArray([
            'file' => [
                'public' => 0777,
                'private' => 0777,
            ],
            'dir' => [
                'public' => 0777,
                'private' => 0777,
            ],
        ]);
        $adapter = new LocalFilesystemAdapter(APPPATH.'../backup_folder');
        $filesystem = new Filesystem($adapter);
        return $filesystem->listContents($folder)
            ->filter(function(StorageAttributes $attributes){
                return $attributes->isDir();
            })
            ->map(function(StorageAttributes $attributes){
                return $attributes->path();
            })->toArray();
    }
}

if(!function_exists('list_file')){
    function list_file($backup_folder,$jenis){
        $arFlyConf = PortableVisibilityConverter::fromArray([
            'file' => [
                'public' => 0777,
                'private' => 0777,
            ],
            'dir' => [
                'public' => 0777,
                'private' => 0777,
            ],
        ]);
        $adapter = new LocalFilesystemAdapter($backup_folder);
        $filesystem = new Filesystem($adapter);
        return $filesystem->listContents($jenis)
            ->filter(function(StorageAttributes $attributes){
                return $attributes->isFile();
            })
            ->map(function(StorageAttributes $attributes){
                return $attributes->path();
            })->toArray();
    }
}

if(!function_exists('create_folder')){
    function create_folder($base_dir,$folder){
        $arFlyConf = PortableVisibilityConverter::fromArray([
            'file' => [
                'public' => 0777,
                'private' => 0777,
            ],
            'dir' => [
                'public' => 0777,
                'private' => 0777,
            ],
        ]);
        $adapter = new LocalFilesystemAdapter($base_dir);
        $filesystem = new Filesystem($adapter);
        $filesystem->createDirectory($folder);
    }
}

if(!function_exists('create_file')){
    function create_file($base_dir,$file,$isi){
        $arFlyConf = PortableVisibilityConverter::fromArray([
            'file' => [
                'public' => 777,
                'private' => 777,
            ],
            'dir' => [
                'public' => 777,
                'private' => 777,
            ],
        ]);
        $adapter = new LocalFilesystemAdapter($base_dir,$arFlyConf);
        $filesystem = new Filesystem($adapter);
        $filesystem->write($file,$isi);
    }
}