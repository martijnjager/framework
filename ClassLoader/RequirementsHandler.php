<?php
namespace ClassLoader;

use DirectoryIterator;

class RequirementsHandler
{
    private $requirements;
    const CURRENT_DIRECTORY = __DIR__.'/../../core';

    private $directories = [];

    public function __construct(array $requirements)
    {
        $this->requirements = $requirements;
    }

    public function handleRequirements()
    {
        $directory = "G:\wamp64\www\Modules";

        foreach(new DirectoryIterator($directory) as $d) {
            $this->directories[] = $d;
        }

        foreach($this->requirements as $requirement) {
            $this->runChecks($requirement);
        }
    }

    public static function handlePreloadRequirements()
    {
        $handler = new RequirementsHandler(['Reflector', 'Filesystem']);
        $handler->handleRequirements();
    }

    private function runChecks($requirement)
    {
        foreach ($this->directories as $d) {
            if($requirement == $d->getFilename() && !$this->requirementPresent($requirement)) {
                $this->move($d->getPathname());
            }
        }
    }

    private function requirementPresent($requirement)
    {
        foreach(new DirectoryIterator(self::CURRENT_DIRECTORY) as $d) {
            if($d->getFilename() == $requirement)
                return true;
        }

        return false;
    }

    private function move($directory)
    {
        while(false !== ($file = readdir(opendir($directory)))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                copy($directory.'/'.$file, self::CURRENT_DIRECTORY);
            }
        }
    }
}
