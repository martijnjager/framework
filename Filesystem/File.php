<?php
namespace Filesystem;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class File
{
    public static function getFilesFrom($dir)
    {
        $directory = new RecursiveDirectoryIterator($dir);

        $files = [];
        foreach(new RecursiveIteratorIterator($directory) as $file) {
            if(strlen($file->getFileName()) > 2 && $file->getFilename() != 'Loader.php'){
                $files[] = $file->getPathName();
            }
        }

        return $files;
    }

    public static function exists($file)
    {
        return @file_exists($file);
    }

    public static function content($file, $json = false)
    {
        if($json){
            return json_encode(file_get_contents($file));
        }

        return file_get_contents($file);
    }

    public static function create($name, $content)
    {
        return file_put_contents($name, json_encode($content));
    }
}
