<?php
namespace Collection;

use ArrayAccess;
use Exception;

class Collection implements ArrayAccess
{
    /**
     * Contains all entries retrieved from the database
     * @param $items
     */
//    private $attributes = [];

    public function __construct($items)
    {
        foreach($items as $key => $value)
        {
            if(!in_array($key, ['password']))
                $this->$key = $value;
        }
    }

    /**
     * Dynamically access the attributes from a database
     * @param string $key
     * @return mixed
     * @throws Exception when $key is not present in array of attributes
     */
    public function __get($key)
    {
        if(in_array($this->$key, $this->all()))
            return $this->$key;

        throw new Exception("$key is an attribute that does not exist in the collection");
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function dump()
    {
        return dd($this->attributes);
    }

    public function all()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if(array_key_exists($offset, $this)){
            return $this->$offset;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}
