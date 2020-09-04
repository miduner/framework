<?php

namespace Midun;

class AliasLoader
{
    public function __construct()
    {
        spl_autoload_register([$this, 'aliasLoader']);
    }

    public function aliasLoader($class)
    {
        $alias = config('app.aliases');

        if (isset($alias[$class])) {
            return class_alias($alias[$class], $class);
        }
    }
}
