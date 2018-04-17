<?php

namespace core\base;

use core\Core;
use core\di\Container;
use core\exceptions\InvalidConfigException;
use core\user\BaseUser;
use core\web\Response;
use Pixie\QueryBuilder;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use core\helpers\ArrayHelper;

/**
 * Class Application
 * @package core\base
 */
class Application extends Module
{
    /**
     * @var string the namespace that controller classes are located in.
     * This namespace will be used to load controller classes by prepending it to the controller class name.
     * The default namespace is `app\controllers`.
     */
    public $controllerNamespace = 'app\\controllers\\';

    /**
     * @var $name string default application name
     */
    public $name = 'My Application';

    /**
     * @var string default appliaction language
     */
    public $language = 'ru';

    /**
     * @var string default system source language
     */
    public $sourceLanguage = 'ru-RU';

    /**
     * @var string controller instance name
     */
    public $controller;

    /**
     * @var string action instance name
     */
    public $action;

    /**
     * @var \core\web\Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var object container request information
     *
     * an example:
     * ```php
     *  if( core\Core::$app->uri->controller == 'index') {
     *      // your handler
     *  }
     *
     *  // print action name
     *  echo \core\Core::$app->uri->action;
     *
     *  //get params
     *  var_dump(\core\Core::$app->uri->params)
     * ```
     */
    public $uri;

    /**
     * @var Application
     */
    public $config;

    /**
     * @var QueryBuilder\QueryBuilderHandler
     */
    public $db;
    /**
     * @var \mysqli
     */
    public $db2;

    /**
     * system user class, example
     *
     * @var \core\user\BaseUser
     */
    public $user;

    /**
     * @var string
     */
    public $_vendorPath;

    /**
     * @var Container
     */
    public $container;

    public function __construct($config = [])
    {
        Core::$app = $this;
        $this->init($config);
        $this->initErrorHandler();
        $this->initConfiguration($config);
        $this->initDatabase();
    }

    public function init($config)
    {
        if (!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration for the Application is required.');
        }

        if (isset($config['basePath'])) {
            $this->setBasePath($config['basePath']);
            unset($config['basePath']);
        } else {
            throw new InvalidConfigException('The "basePath" configuration for the Application is required.');
        }

        if (isset($config['timeZone'])) {
            $this->setTimeZone($config['timeZone']);
            unset($config['timeZone']);
        } elseif (!ini_get('date.timezone')) {
            $this->setTimeZone('UTC');
        }

        $this->setBasePaths(dirname(dirname(__DIR__)));
        $this->response = new Response();
        $this->request = new \core\web\Request();
        $this->user = new BaseUser();
        $this->container = new Container();

        foreach ($this->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }

        foreach ($config['components'] as $id => $component){
            $this->container->set($id, $component['class']);
        }
    }

    /**
     * Application configuration initialization, an example of how you can set the system settings.
     */
    public function initConfiguration($config)
    {
        $this->config = new Registry($config);
    }

    /**
     * Application error handler initialization, here you can initialize any other or your error handlers.
     */
    public function initErrorHandler()
    {
        (new Run())
            ->pushHandler(new PrettyPageHandler())
            ->register();

        error_reporting(E_ALL & ~E_NOTICE);
    }

    public function initDatabase()
    {
        // your connetcion to DB adapter and query builder
        // or system core\db\Connection and init builder pdo()
        $this->db2 = new \mysqli($this->config->db['host'], $this->config->db['user'], $this->config->db['password'], $this->config->db['dbname']);
        if ($this->db2->connect_error) {
            die('Connection Error (' . $this->db2->connect_errno . ') '. $this->db2->connect_error);
        }
    }

    /**
     * todo: example
     *
     * Returns the configuration of core application components.
     * @see set()
     */
    public function coreComponents()
    {
        return [
            'view' => ['class' => 'core\web\View'],
        ];
    }

    /**
     * Application base paths(alias) init
     *
     * @param $path
     */
    public function setBasePaths($path)
    {
        parent::setBasePath($path);
        $this->setVendorPath($this->getBasePath().DIRECTORY_SEPARATOR . 'vendor');
        Core::setAlias('@core', $this->getBasePath(). DIRECTORY_SEPARATOR . 'core');
        Core::setAlias('@app', $this->getBasePath() . DIRECTORY_SEPARATOR . 'app');
        Core::setAlias('@api', $this->getBasePath() . DIRECTORY_SEPARATOR . 'api');
        Core::setAlias('@views', '@app/views');
        Core::setAlias('@widgets', '@app/widgets');
        $this->setViewPath('@views');
    }

    /**
     * @return \mysqli
     */
    public function getDb()
    {
        return $this->db2;
    }

    /**
     * Returns the directory that stores vendor files.
     * @return string the directory that stores vendor files.
     * Defaults to "vendor" directory under [[basePath]].
     */
    public function getVendorPath()
    {
        if ($this->_vendorPath === null) {
            $this->setVendorPath($this->getBasePath() . DIRECTORY_SEPARATOR . 'vendor');
        }

        return $this->_vendorPath;
    }

    /**
     * Sets the directory that stores vendor files.
     * @param string $path the directory that stores vendor files.
     */
    public function setVendorPath($path)
    {
        $this->_vendorPath = Core::getAlias($path);
        Core::setAlias('@vendor', $this->_vendorPath);
        Core::setAlias('@bower', $this->_vendorPath . DIRECTORY_SEPARATOR . 'bower');
        Core::setAlias('@npm', $this->_vendorPath . DIRECTORY_SEPARATOR . 'npm');
    }

    /**
     * Returns the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_get().
     * If time zone is not configured in php.ini or application config,
     * it will be set to UTC by default.
     * @return string the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }

    /**
     * Sets the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_set().
     * Refer to the [php manual](http://www.php.net/manual/en/timezones.php) for available timezones.
     * @param string $value the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-set.php
     */
    public function setTimeZone($value)
    {
        date_default_timezone_set($value);
    }

    /**
     * Run apllication, an example of how you can run the application.
     * You can use either or your router to route requests and run the desired action controller.
     */
    public function run(){
        Router::gi()->addRoute($this->config->routes);
        Router::gi()->parse($_SERVER['REQUEST_URI']);
    }
}
