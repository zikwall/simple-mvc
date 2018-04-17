<?php

namespace core\di;

use Core;
use core\base\Component;

class Container extends Component
{
    /**
     *
     * @var array
     */
    protected $_singletons = [];

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->_singletons[$name]($this);
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->_singletons[$name] = $value;
    }
}
