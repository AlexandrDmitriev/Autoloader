<?php

namespace AutoLoader;

class AutoLoader
{
    private $loader;
    private $namespaces;
    private $paths = array();

    public function __construct(array $namespaces, Loader $loader = null)
    {
        $this->namespaces = $namespaces;

        if ($loader === null) {
            include 'Loader.php';
            $this->loader = new Loader();
        } else {
            $this->loader = $loader;
        }
        $this->register();
    }

    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }


    private function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    protected function replaceNameSpaces($className)
    {
        return $className;
    }

    protected function tryResolveLikeSpecificPath($className)
    {
        if (array_key_exists($className, $this->paths)) {
            return $this->loader->loadFile($this->paths[$className]);
        }

        return false;
    }

    protected function resolveRegular($className)
    {
        $path = $this->replaceNameSpaces($className);
        return $this->loader->loadFile($path);
    }

    public function loadClass($className)
    {
        if (!$this->tryResolveLikeSpecificPath($className)) {
            $this->resolveRegular($className);
        }
    }
}