<?php

namespace Midun;

class AliasLoader
{
    /**
     * Initial miduner alias loader
     * 
     * @method aliasLoader()
     */
    public function __construct()
    {
        spl_autoload_register([$this, 'aliasLoader']);
    }

    /**
     * Listen loading classes
     * 
     * @param string $class
     * 
     * @return bool
     */
    public function aliasLoader(string $class): bool
    {
        $alias = config('app.aliases');

        if (isset($alias[$class])) {
            return class_alias($alias[$class], $class);
        }

        return true;
    }
}
