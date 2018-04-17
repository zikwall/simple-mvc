<?php

namespace core\base;

use core\exceptions\InvalidCallException;

Abstract class Singleton{

    private static $_aInstances = [];

    /**
     * @param bool $className
     * @return mixed
     */
    public static function getInstance( $className = false ) {
        $sClassName = ($className === false) ? get_called_class() : $className;

        if( class_exists($sClassName) ){
            if( !isset( self::$_aInstances[ $sClassName ] ) )
                self::$_aInstances[ $sClassName ] = new $sClassName();
            $oInstance = self::$_aInstances[ $sClassName ];
            return $oInstance;
        }else
            throw new InvalidCallException('Class '.$sClassName.'  no exist!');
    }

    /**
     * @param bool $className
     * @return mixed
     */
    public static function gi( $className = false ) {
        return self::getInstance($className);
    }

    function __construct() {
    }

    private function __clone() {
    }

    private function __sleep() {
    }

    private function __wakeup() {
    }
}