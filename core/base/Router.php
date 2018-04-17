<?php
namespace core\base;

use core\Core;
use core\exceptions\InvalidCallException;

class Router extends Singleton
{

    private static $params = [];
    private static $pathElements = ['controller', 'action', 'id'];
    private static $getParams = [];

    public static $routes = [];
    public static $requestedUrl = '';

    public static $defaultRoutes = [
        '([a-z0-9+_\-]+)/([a-z0-9+_\-]+)/([0-9]+)' => '$controller/$action/$id',
        '([a-z0-9+_\-]+)/([a-z0-9+_\-]+)' => '$controller/$action',
        '([a-z0-9+_\-]+)(/)?' => '$controller',
    ];

    public $controller;

    /**
     * Добавить маршрут
     */
    public static function addRoute($route, $destination = null)
    {
        if ($destination != null && !is_array($route)) {
            $route = array($route => $destination);
        }
        self::$routes = array_merge(self::$routes, $route);
    }

    /**
     * Разделить переданный URL на компоненты
     */
    public static function splitUrl($url)
    {
        return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Текущий обработанный URL
     */
    public static function getCurrentUrl()
    {
        return (self::$requestedUrl ? :'/');
    }

    public static function getRequestParams()
    {
        $result = substr(strstr($_SERVER["REQUEST_URI"], '?'), 1, strlen($_SERVER["REQUEST_URI"]));
        parse_str($result, $params);
        return $params;
    }

    /**
     * Обработка переданного URL
     */
    public static function parse($requestedUrl = null)
    {
        // Если URL не передан, берем его из REQUEST_URI
        if ($requestedUrl === null) {
            $uri = reset(explode('?', $_SERVER["REQUEST_URI"]));
            $position = strpos($uri, '?');
            if ($position !== false) {
                $uri = mb_substr($uri, 0, $position);
            }
            $requestedUrl = urldecode(rtrim($uri, '/'));
        }

        self::$requestedUrl = $requestedUrl;

        // если URL и маршрут полностью совпадают
        if (isset(self::$routes[$requestedUrl])) {
            self::$params = self::splitUrl(self::$routes[$requestedUrl]);
            return self::executeAction();
        }


        foreach (self::$routes as $route => $uri) {
            // Заменяем wildcards на рег. выражения
            if (strpos($route, ':') !== false) {
                $route = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $route));
            }

            if (preg_match('#^'.$route.'$#', $requestedUrl)) {
                if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {
                    $uri = preg_replace('#^'.$route.'$#', $uri, $requestedUrl);
                }
                self::$params = self::splitUrl($uri);

                break; // URL обработан!
            }
        }

        return self::executeAction();
    }

    /**
     * Запуск соответствующего действия/экшена/метода контроллера
     */
    public static function executeAction()
    {
        $controller = isset(self::$params[0]) ? self::$params[0]: Core::$app->config->default_controller.'Controllers';
        $action = isset(self::$params[1]) ? self::$params[1]: 'action'.Core::$app->config->default_action;
        $params = array_slice(self::$params, 2);

        if(!empty($controller) && !empty($action)){
            Core::$app->uri = new Registry([
                'controller' => strtolower(str_replace('Controller','',$controller)),
                'action' => strtolower(str_replace('action','',$action)),
                'params' => self::getRequestParams()
            ]);
            return self::_call($controller, $action, $params);
        }

        throw new InvalidCallException('Action ' . $action . 'in Controller ' . $controller . 'executting failed!');
    }

    public static function _call($controller, $action, $params = [])
    {
        $namespace = Core::$app->controllerNamespace;

        if (class_exists($namespace.$controller)) {
            $controllerNamespacingClass = $namespace.$controller;
            $controller = new $controllerNamespacingClass;
            if($controller->beforeAction()){
                if (method_exists($controller, $action))
                    return call_user_func_array([$controller, $action], $params);
                else {
                    throw new InvalidCallException('In controller ' . $controllerNamespacingClass . ' method ' . $action . ' not found!');
                }
            }
        } else {
            throw new InvalidCallException('In application controller class ' . $controller . ' not found!');
        }
    }

}