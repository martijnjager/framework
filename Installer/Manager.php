<?php

namespace Installer\Manager;

use ClassLoader\JsonParser;
use ClassLoader\Mapper;
use Filesystem\File;

class InstallManager{

    public $mapper = [];

    public function __construct()
    {
        foreach(File::getFilesFrom('G:\wamp64\www\Modules') as $array) {
            var_dump($array);
            die();
        }
    }
}
