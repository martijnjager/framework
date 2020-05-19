<?php

namespace Database\Grammar;

use Database\Eloquent;
use Exception;
use ReflectionProperty;

class GrammarCompiler
{
    use Grammar;

    /**
     * List of clauses that can be dynamically compiled
     * Make sure to add the related sql in the Grammar class if this list is extended
     *
     * @var array
    */
    private $options = ['from', 'join', 'where', 'groupBy', 'having', 'orderBy', 'limit'];

    private $order = ['select', 'from', 'join', 'where', 'groupBy', 'having', 'orderBy', 'limit'];

    public static function compile(array $clauses)
    {
//        $props = app()->getProperties($eloquent)[0]->getValue($eloquent);
//        dd($props, app()->getProperties($eloquent)[0], $eloquent);
//        return (new self())->getValues($props);

        try {
            return (new self())->getValues($clauses);
        } catch (Exception $e) {
        }
    }

    /**
     * @param ReflectionProperty[] $properties
     * @return string
     * @throws Exception
     */
    private function getValues(array $properties)
    {
        $query = "";

        foreach($this->order as $item)
        {
            if(array_key_exists($item, $properties) && !empty($properties[$item]))
            {
//                foreach($properties as $property => $value)
//                {
//                    if(!empty($value))
//                    {
                        $query .= $this->prepareClause($item, $properties[$item]);
//                    }
//                }
            }
        }

        return $query . $this->compileEOS();
    }

    /**
     * @param $property
     * @param $values
     * @return string
     * @throws Exception if $property is not present
     */
    private function prepareClause($property, $values)
    {
        if($property == 'select') {
            return $this->handleSelect($values);
        }
        else {
            if(in_array($property, $this->options) && !empty($values))
            {
                // Handle the prepping without explicitly calling the related function
                $function = 'compile'.$property;
                return $this->$function($values);
            }
        }

        throw new Exception("Clause $property could not be included in the query");
    }

    private function handleSelect($values)
    {
        switch($values)
        {
            case is_array($values):
                return $this->compileSelectColumn($values);
                break;
            case '*':
                return $this->compileSelect();
                break;
            case 'count(*)':
                return $this->compileSelectCount();
                break;

            default:
                return $this->compileSelectCountColumn($values);
                break;
        }
    }
}
