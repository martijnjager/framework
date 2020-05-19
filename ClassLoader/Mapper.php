<?php
namespace ClassLoader;

use Filesystem\File;

class Mapper
{
    private $files = [];

    private $extensions = [];

    public function __construct($dir)
    {
        $this->files = File::getFilesFrom($dir);
    }

    public function map()
    {
        foreach ($this->files as $file)
        {
            $this->findExtensionsFor($file);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

//    public function storeDependeciesAsJson()
//    {
//        File::create('dependency.json', ["dependencies" => $this->getExtensions()]);
//        return $this;
//    }

    private function hasExtension($file)
    {
        return stripos($file,'extends') !== false && stripos($file, 'class') !== false && stripos($file, "Mapper") === false;
    }

    protected function findExtensionsFor($file)
    {
        $content = explode('\r\n', File::content($file, true));
        foreach($content as $line)
        {
            if($this->hasExtension($line))
            {
                $c = substr($line, stripos($line, 'extends ') + strlen('extends '), strlen($line));
                $this->extensions[$file] = $c;
            }
        }

        if(empty($this->extensions[$file]))
        {
            $this->extensions[$file] = null;
        }
    }
}
