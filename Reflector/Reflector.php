<?php
namespace Reflection;

use Exception;
use ReflectionParameter;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;

class Reflector
{
    /**
     * @param $class
     * @return mixed|object
     * @return mixed|object
     * @throws Exception if $class is not instantiable
     */
    public function resolve($class)
    {
        $class = str_ireplace('/', '\\', $class);

        $reflector = new ReflectionClass($class);


        if($reflector->isInstantiable())
        {
            $constr = $reflector->getConstructor();

            if(!is_null($constr))
            {
                $parameters = $constr->getParameters();

                $dependencies = $this->getDependencies($parameters);

                return $reflector->newInstanceArgs($dependencies);
            }

            return new $class;
        }

        throw new Exception("$class is not instantiable");
    }

    /**
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getDependencies(array $parameters)
    {
        $dependencies = array();

        foreach($parameters as $parameter)
        {
            if($this->has($parameter))
            {
                if($parameter instanceof ReflectionParameter)
                {
                    $dependencies[] = $this->get($parameter);
                }
            }
            else
            {
                if($parameter instanceof ReflectionParameter) {
                    if(!is_null($parameter->getClass()))
                        $dependencies[] = $this->resolve($parameter->getClass()->getName());
                }
            }
        }

        return $dependencies;
    }

    /**
     * Gets all properties from a class, any private or protected properties are temporarily set to public to make them accessible
     * @param $class
     * @return ReflectionObject|ReflectionProperty[]
     */
//    public function getProperties($class)
//    {
//        $r = new ReflectionObject($class);
//        $r = $r->getProperties();
//        for($i = 0; $i < count($r); $i++)
//            $r[$i]->setAccessible(true);
//
//        return $r;
//    }
}
