<?php
namespace Collection;

use Countable;
use Exception;
use ArrayAccess;
use Iterator;

class Collections implements Countable, ArrayAccess, Iterator
{
    /**
     * Contains all entries retrieved from the database
     */
    private $attributes = [];

    public function __construct(array $items)
    {
        // Looping through the entries retrieved from the database
        for($i = 0; $i < count($items); $i++){
            // We're storing the entries using a new class so we can dynamically access them later (see __get function)
            $this->attributes[] = new Collection($items[$i]);
        }
    }

    /**
     * Dynamically access the attributes from a database
     * @param string $key
     * @return mixed
     * @throws Exception if $key is not present in attributes array
     */
    public function __get($key)
    {
        if(array_key_exists($key, $this->attributes)){
            return $this->attributes[$key];
        }
    }

    public function count()
    {
        return count($this->attributes);
    }

    public function first()
    {
        if(!empty($this->attributes))
            return $this->attributes[0];

        return null;
    }

    public function dump()
    {
        return dd($this->attributes);
    }

    public function all()
    {
        if(!empty($this->attributes))
            return $this->attributes;

        return '';
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if($this->offsetExists($offset))
            return $this->attributes[$offset];
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->attributes);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        return next($this->attributes);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->attributes);
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        $key = key($this->attributes);
        return (!is_null($key) && $key !== false);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->attributes);
    }
}
