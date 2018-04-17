<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 12.01.2018
 * Time: 16:49
 */

namespace core\base;

use core\Core;
use core\exceptions\InvalidCallException;

class BaseWidget
{
    /**
     * @var View
     */
    public $view;

    /**
     * @var Widget
     */
    public $widget;

    public $_viewPath = null;

    protected function configure($config = [])
    {
        if (is_array($config) && count($config)) {
            foreach ($config as $name => $value) {
                $this->$name = $value;
            }
        }

        return $this;
    }

    public function getView()
    {
        if ($this->view === null) {
            $this->view = Core::$app->getView();
        }

        return $this->view;
    }

    public function getViewPath()
    {
        $class = new \ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'views';
    }

    public function getWidgetsPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = Core::getAlias('@widgets') . DIRECTORY_SEPARATOR . 'views';
        }
        return $this->_viewPath;
    }

    public function beforeRun()
    {
        return true;
    }

    public function afterRun($result)
    {
        return $result;
    }

    public function run()
    {

    }

    public static function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            $widgetClass = get_called_class();
            if (class_exists($widgetClass)) {
                /**
                 * @var $widget Widget
                 */
                $widget = new $widgetClass;

                if($widget->beforeRun()){
                    $widget->configure($config);
                    $result = $widget->run();
                    $out = $widget->afterRun($result);
                }
            }
        } catch (\Exception $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
        return ob_get_clean() . $out;
    }

    private function _renderPartial($fullpath, $variables = [], $output = true)
    {
        extract($variables);

        if( file_exists($fullpath) ){
            if( !$output )
                ob_start();
            include $fullpath;
            return !$output ? ob_get_clean() : true;
        } else
            throw new InvalidCallException('File '.$fullpath.' not found');

    }

    public function render($filename, $variables = [], $output = true)
    {
        $file = $this->getViewPath() . DIRECTORY_SEPARATOR . str_replace('..','', $filename) . '.php';
        return static::_renderPartial($file, $variables, $output);
    }
}