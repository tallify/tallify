<?php

use Illuminate\Container\Container;

class MyFacade
{
    /**
     * The key for the binding in the container.
     *
     * @return string
     */
    public static function containerKey()
    {
        return 'Tallify\\' . basename(str_replace('\\', '/', get_called_class()));
    }

    /**
     * Call a non-static method on the facade.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $resolvedInstance = Container::getInstance()->make(static::containerKey());

        return call_user_func_array([$resolvedInstance, $method], $parameters);
    }
}

class Build extends MyFacade
{
}

class Config extends MyFacade
{
}

class Command extends MyFacade
{
}

class Output extends MyFacade
{
}

class Question extends MyFacade
{
}
