<?php

namespace AutoLoader;

class AutoLoader
{
    private $loader;
    private $projectsNamespaces;
    private $paths = array();
    private $aliases = array();
    private $appRoot;

    public function __construct($appRoot, array $projectsNamespaces, Loader $loader = null)
    {
        $this->projectsNamespaces = $projectsNamespaces;
        $this->appRoot = $appRoot;

        if ($loader === null) {
            include 'Loader.php';
            $this->loader = new Loader();
        } else {
            $this->loader = $loader;
        }
        $this->register();
    }

    public function addPaths(array $paths)
    {
        $this->paths = array_merge($this->paths, $paths);
    }

    public function removePath($path)
    {
        unset($this->paths[$path]);
    }

    public function addAliases(array $aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function removeAliases($alias)
    {
        unset($this->paths[$alias]);
    }

    private function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    protected function placeHoldersReplace($path)
    {
        $path = preg_replace_callback(
            '/{{([^}]+)}}/',
            function ($matches) {
                if (empty($matches[1])) {
                    return $matches[0];
                }
                switch ($matches[1]) {
                    case 'root':
                        return $this->appRoot;
                    default:
                        return isset($this->aliases[$matches[1]]) ? $this->aliases[$matches[1]] : $matches[0];
                }
            },
            $path
        );
        return $path;
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
            return $this->appRoot . $className;
        }

        return sprintf('%s/%s', $this->appRoot, $className);
    }

    protected function normalizePath($path)
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            return str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        return str_replace('\\', DIRECTORY_SEPARATOR, $path);
    }

    protected function loadFile($fileName)
    {
        $fileName = $this->placeHoldersReplace($fileName);

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

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
}
