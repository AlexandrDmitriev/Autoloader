<?php

namespace AutoLoader;

class AutoLoader
{
    private $loader;
    private $projectsNamespaces;
    private $paths = array();

    public function __construct(array $projectsNamespaces, Loader $loader = null)
    {
        $this->projectsNamespaces = $projectsNamespaces;

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
        $aliases = array_keys($this->projectsNamespaces);

        foreach ($aliases as $alias) {
            if (strpos($className, $alias) === 0) {
                return $this->projectsNamespaces[$alias].substr($className, strlen($alias));
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
