<?php
namespace Database;

use Database\Grammar\GrammarCompiler;
use Exception;
use ReflectionClass;
use ReflectionException;

class Saver extends Database
{
    private $attributes;
    private $values;
    private $parameters;

    public function __set($name, $value)
    {
        $this->attributes[] = $name;
        $this->values[] = $value;
        $this->parameters = ':'.$name;
    }

    public function save()
    {
        $grammarCompiler = new GrammarCompiler();
        $sql = $grammarCompiler->compileInsert($this->getTable(), $this->columns(), $this->parameters());

        try {
            $this->runQuery($sql, $this->values());
        } catch (Exception $e) {
        }
    }

    protected function getTable()
    {
        try {
            $class = new ReflectionClass($this);
        } catch (ReflectionException $e) {
        }

        return substr($class->name, strpos($class->name, '\\') + 1, (int)strlen($class->name));
    }

    private function parameters()
    {
        return implode(', ', $this->parameters);
    }

    private function columns()
    {
        return implode(', ', $this->attributes);
    }

    private function values()
    {
        return array_combine($this->parameters, $this->values);
    }
}
