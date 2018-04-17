<?php

namespace core\base;

use core\Core;
use core\exceptions\InvalidParamException;

class Module extends Component
{
    /**
     * @var string the root directory of the module.
     */
    private $_basePath;

    private $_viewPath;

    /**
     * @var string
     */
    private $_layoutPath;

    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass($this);
            $this->_basePath = dirname($class->getFileName());
        }

        return $this->_basePath;
    }

    public function setBasePath($path)
    {
        $path = Core::getAlias($path);
        $p = strncmp($path, 'phar://', 7) === 0 ? $path : realpath($path);
        if ($p !== false && is_dir($p)) {
            $this->_basePath = $p;
        } else {
            throw new InvalidParamException("The directory does not exist: $path");
        }
    }

    public function getControllerPath()
    {
        return Core::getAlias('@' . str_replace('\\', '/', $this->controllerNamespace));
    }

    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'views';
        }
        return $this->_viewPath;
    }

    public function setViewPath($path)
    {
        $this->_viewPath = Core::getAlias($path);
    }

    public function getLayoutPath()
    {
        if ($this->_layoutPath === null) {
            $this->_layoutPath = $this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts';
        }

        return $this->_layoutPath;
    }

    public function setLayoutPath($path)
    {
        $this->_layoutPath = Core::getAlias($path);
    }
}