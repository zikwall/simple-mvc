<?php

namespace core;

/**
 * Class Core
 * @package core
 */
class Core extends BaseCore
{
    public static function className()
    {
        return get_called_class();
    }

    public function setAliases($aliases)
    {
        foreach ($aliases as $name => $alias) {
            Core::setAlias($name, $alias);
        }
    }
}

spl_autoload_register([Core::class, 'autoload'], true, true);