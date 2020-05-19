<?php
namespace Request;

use Exception;

class Request
{
    private $request;

    public function __construct(array $data = null)
    {
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $this->request->$key = $value;
            }
        }
        else
            $this->request = [];
    }

    /**
     * Dynamically access the attributes from a request response
     * @param string $key
     * @return mixed
     * @throws Exception when $key is not present in array of attributes
     */
    public function __get($key)
    {
        if(array_key_exists($key, $this->request)){
            return $this->request[$key];
        }

        throw new Exception("$key is not in array of attributes");
    }

    public function __set($key, $value)
    {
        $this->request[$key] = $value;
    }

    public function has($key)
    {
        if(is_array($key))
        {
            foreach ($key as $value)
            {
                if(!$this->has($value))
                    return false;
            }

            return true;
        }

        if(isset($this->request[$key]) && !empty($this->request[$key]))
            return true;

        return false;
    }

    public function submit()
    {
        if($this->has('submit'))
            return true;

        return false;
    }
}
