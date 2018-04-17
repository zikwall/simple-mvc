<?php

namespace core\base;

use core\exceptions\InvalidParamException;
use core\helpers\ArrayHelper;

class Registry{

    private $data = array();

    function __construct($data = array()) {
        $this->data = $data;
    }

    function __get($name){
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    function __set($name, $value){
        if (!ArrayHelper::keyExists($name, $this->data)) {
            foreach ($this->data as $val) {
                if ($val === $value) {
                    throw new InvalidParamException('Item already exists');
                }
            }
            $this->data[$name] = $value;
        }
    }

    function remove($name) {
        if (ArrayHelper::keyExists($name, $this->data)) {
            unset($this->data[$name]);
        }
    }
}