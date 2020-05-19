<?php
namespace ClassLoader;

use Manager\Container\Application;
use Filesystem\File;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Loader
{
    private static $loadedFiles = [];

    private static $classes;

    public static function load($files)
    {
        if(is_array($files))
            foreach ($files as $f)
                self::loadFile($f);
        else
            self::loadFile($files);
    }

    /**
     * @return array SplFileInfo
    */
    public static function getLoadedFiles()
    {
        return self::$loadedFiles;
    }

    public static function isLoaded($file)
    {
        if($file instanceof SplFileInfo)
            $file = $file->getPathname();

        foreach(self::getLoadedFiles() as $key)
            if(stripos($key->getPathName(), $file) !== false || stripos($file, $key->getPathName()) !== false)
                return true;

        return false;
    }

    private static function getExtensionsFor($class)
    {
        foreach(self::$classes as $extension => $classes)
            if(stripos($extension, $class.'.php') !== false) return $extension;

        throw new InvalidArgumentException("$class not found in list of classes.");
    }

    private static function preloadRequirements()
    {
        self::loadFrom(['core\\Reflector', 'core\\Filesystem']);
        self::loadFile('core\\ClassLoader\\Mapper.php');
    }

    public static function initialize()
    {
        self::preloadRequirements();

        self::$classes = (new Mapper())->map()->getExtensions();

        foreach(self::$classes as $key => $extension)
        {
            if(!is_null($extension) && !self::isLoaded($extension))
                self::loadClass($extension);

            self::loadFile($key);
        }

        return Application::getInstance();
    }

    private static function loadClass($class)
    {
        $trueClass = self::getExtensionsFor($class);

        if(self::$classes[$trueClass] != null)
            self::loadClass(self::$classes[$trueClass]);

        self::loadFile($trueClass);
    }

    public static function loadFrom($directories)
    {
        if(is_array($directories))
            foreach($directories as $directory)
                self::getFrom($directory);
        else
            self::getFrom($directories);
    }

    private static function getFrom($directory)
    {
        $directory = new RecursiveDirectoryIterator($directory);

        foreach(new RecursiveIteratorIterator($directory) as $file)
            if(strlen($file->getFileName()) > 2 )
                self::loadFile($file);
    }

    private static function addFile(SplFileInfo $file)
    {
        self::$loadedFiles[] = $file;
    }

    public static function loadFile($file)
    {
        if(@file_exists($file) && !self::isLoaded($file)){
            self::addFile(new SplFileInfo($file));

            require_once $file;
        }
    }
}
