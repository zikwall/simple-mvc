<?php

namespace core\base;

use core\Core;
use core\exceptions\InvalidCallException;

Class View extends Component {

    /**
     * @var mixed custom parameters that are shared among view templates.
     */
    public $params = [];

    /**
     * @var string
     */
    public $_layout = 'layout';

    /**
     * @var string
     */
    public $_viewFile;

    /**
     * @var string
     */
    public $_viewPath;

    /**
     * @var string
     */
    public $defaultExtension = 'php';

    /**
     * View constructor.
     * @param array $className
     */
    public function __construct($className)
    {
        $this->_viewFile = $className;
    }

    /**
     * @param $viewFile
     * @param $params
     * @return bool
     */
    public function beforeRender($viewFile, $params)
    {
        return true;
    }

    /**
     * Получить отрендеренный шаблон с параметрами $params
     *
     * @param $template
     * @param array $params
     * @return string
     */
    public function fetchPartial($template, $params = [])
    {
        extract($params);
        ob_start();
        include Core::getAlias('@views'). DIRECTORY_SEPARATOR . $template . '.' . $this->defaultExtension;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * вывести отрендеренный шаблон с параметрами $params
     *
     * @param $template
     * @param array $params
     */
    public function renderPartial($template, $params = [])
    {
        echo $this->fetchPartial($template, $params);
    }

    /**
     * получить отрендеренный в переменную $content layout-а
     * шаблон с параметрами $params
     *
     * @param $view
     * @param array $params
     * @return string
     */
    public function fetch($view, $params = [])
    {
        $content = $this->fetchPartial($this->_viewFile. DIRECTORY_SEPARATOR .$view, $params);
        return $this->fetchPartial('layouts'. DIRECTORY_SEPARATOR .$this->_layout, [
            'content' => $content,
            'this' => $this->_viewFile
        ]);
    }

    /**
     * вывести отрендеренный в переменную $content layout-а
     * шаблон с параметрами $params
     *
     * @param $template
     * @param array $params
     */
    public function render($template, $params = [])
    {
        echo $this->fetch($template, $params);
    }

    public function findViewFile($view)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Core::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = Core::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        } else {
            return $view;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        $path = $file . '.' . $this->defaultExtension;

        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     * Marks the beginning of a page.
     */
    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);
    }
    /**
     * Marks the ending of a page.
     */
    public function endPage()
    {
        ob_end_flush();
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

}