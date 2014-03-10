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
        $aliases = array_keys($this->namespaces);

        foreach ($aliases as $alias) {
            if (strpos($className, $alias) === 0) {
                return str_replace($alias, $this->namespaces[$alias], $className);
            }
        }

        if (substr($className, 0, 1) == '\\') {
            return '.' . $className;
        }

        return './' . $className;
    }

    protected function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

    protected function loadFile($fileName)
    {
        $fileName = $this->normalizePath($fileName);

        return $this->loader->loadFile($fileName);
    }

    protected function tryResolveLikeSpecificPath($className)
    {
        if (array_key_exists($className, $this->paths)) {
            return $this->loadFile($this->paths[$className]);
        }

        return false;
    }

    protected function resolveRegular($className)
    {
        $path = $this->replaceNameSpaces($className).'.php';

        return $this->loadFile($path);
    }

    public function loadClass($className)
    {
        if (!$this->tryResolveLikeSpecificPath($className)) {
            $this->resolveRegular($className);
        }
    }
}
