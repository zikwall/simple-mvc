<?php
namespace core\base;

use core\Core;
use core\exceptions\InvalidCallException;
use core\exceptions\InvalidConfigException;
use core\exceptions\InvalidParamException;
use core\web\Response;
use core\helpers\ArrayHelper;

Abstract Class Controller extends Module
{

    /**
     * @var View
     */
    public $view;

    /**
     * @var string instance view path
     */
    private $_viewPath;

    public $defaultAction = 'index';

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->view = new View($this->getControllerName());
    }

    /**
     * @return bool
     */
    public function beforeAction()
    {
        return true;
    }

    public function afterAction()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param $newLayout
     */
    public function setLayout($newLayout)
    {
        $this->view->_layout = $newLayout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->view->_layout;
    }

    /**
     * @param $template
     * @param array $params
     */
    public function render($view, $params = [])
    {
        $this->view->render($view, $params);
    }

    /**
     * This method is called right before `run()` is executed.
     * You may override this method to do preparation work for the action run.
     * If the method returns false, it will cancel the action.
     *
     * @return bool whether to run the action.
     */
    protected function beforeRun()
    {
        return true;
    }

    /**
     * This method is called right after `run()` is executed.
     * You may override this method to do post-processing work for the action run.
     */
    protected function afterRun()
    {}

    public function run($route, $params = [])
    {
        return $this->runAction($route, $params);
    }

    public function runAction($id, $params = [])
    {
        $action = 'action'.$id;
        if(!method_exists($this, $action)){
            throw new InvalidCallException();
        }

        if ($this->beforeAction()) {
            // run the action
            $result = $this->runWithParams($params);

            return $result;
        }

        throw new InvalidCallException();
    }

    public function runWithParams($params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }
        if ($this->beforeRun()) {
            $result = call_user_func_array([$this, 'run'], $params);
            $this->afterRun();

            return $result;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return strtolower(str_replace('Controller','',$this->getName()));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        $path = explode('\\', get_called_class());
        return array_pop($path);
    }

    public function goHome()
    {
        $this->redirect('/');
    }

    public function goLogin()
    {
        $this->redirect('/auth/login');
    }

    public static function redirect($url, $httpResponseCodeParam = null)
    {
        if ($httpResponseCodeParam) {
            if (ArrayHelper::keyExists($httpResponseCodeParam, Response::STATUS_CODE)) {
                $httpResponseCode = $httpResponseCodeParam;
                header('Location: '.$url, true, $httpResponseCode);
            } else {
                throw new InvalidParamException('Status code "'.$httpResponseCodeParam.'" not good.');
                //header('Location: '.$url);
            }
        } else {
            header('Location: '.$url);
        }

        exit();
    }

}