<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 30.01.2018
 * Time: 21:00
 */

namespace core\web;


class Input
{
    /**
     * @param string $name
     * @return bool
     */
    public static function hasPost($name)
    {
        return (array_key_exists($name, $_POST));
    }

    /**
     * @param string $name
     * @return array|null
     */
    public static function post($name)
    {
        return (isset($_POST[$name]) && $_POST[$name] != '') ? $_POST[$name] : '';
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasGet($name)
    {
        return (array_key_exists($name, $_GET));
    }

    /**
     * @param string $name
     *  @return array|null - Donnée envoyée en GET
     */
    public static function get($name)
    {
        return (isset($_GET[$name]) && $_GET[$name] != '') ? $_GET[$name] : '';
    }
}