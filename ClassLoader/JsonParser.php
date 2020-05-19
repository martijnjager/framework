<?php
namespace ClassLoader;


class JsonParser
{
    protected $modules = [];

    public function parse($file){
        $content = explode("\r\n", $file);
        $content = $this->trim($content);

        $this->doParse($content);

        return $this->modules;
    }

    private function trim($content)
    {
        $newContent = [];
        for ($i = 1; $i < count($content) - 2; $i++) {
            $newContent[] = trim($content[$i]);
        }

        return $newContent;
    }

    private function doParse($content)
    {
        $newContent = [];
        foreach($content as $value) {
            $value = str_replace('{', '', $value);
            $value = str_replace('}', '', $value);
            $value = str_replace(',', '', $value);
            $value = str_replace('"', '', $value);
            $value = explode(': ', $value);
            $newContent[] = $value;
        }

        $this->modules = $newContent;
        $modules = [];

        for($i = 0; $i < count($this->modules); $i++) {
            if(count($this->modules[$i]) > 1) {
                $module = $this->modules[$i];

                if($module[0] == 'dependencies') {
                    $this->parseDependencies($modules, ++$i);
                    return;
                }

                $modules[$module[0]] = $module[1];
            }
        }
    }

    private function parseDependencies($modules, $index)
    {
        while($index < count($this->modules)) {
            $module = $this->modules[$index];
            if(strlen($module[0]) > 0) {
                $modules['dependencies'][] = $this->modules[$index][0];
            }

            $index++;
        }

        $this->modules = $modules;
    }
}
