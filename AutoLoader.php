<?php

namespace AutoLoader;

use CoreInterfaces\IAutoLoader;

class AutoLoader implements IAutoLoader
{
    protected $loader;
    protected $projectsNamespaces;
    protected $paths = array();
    protected $aliases = array();

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

    public function addPaths(array $paths)
    {
        $this->paths = array_merge($this->paths, $paths);
    }

    public function removePath($path)
    {
        unset($this->paths[$path]);
    }

    public function addAliases(array $alias)
    {
        $this->aliases = array_merge($this->aliases, $alias);
    }

    public function removeAliases($alias)
    {
        unset($this->paths[$alias]);
    }

    public function loadClass($className)
    {
        if (array_key_exists($className, $this->paths)) {
            return $this->loadFile($this->paths[$className]);
        }

        foreach ($this->projectsNamespaces as $alias => $path) {
            if (strpos($className, $alias) === 0) {
                return $this->loadFile(
                    sprintf('%s%s.php', $path, substr($className, strlen($alias)))
                );
            }
        }

        return false;
    }

    protected function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    protected function placeHoldersReplace($path)
    {
        $path = preg_replace_callback(
            '/{([^}]+)}/',
            function ($matches) {
                if (empty($matches[1])) {
                    return $matches[0];
                }
                return isset($this->aliases[$matches[1]]) ? $this->aliases[$matches[1]] : $matches[0];
            },
            $path
        );
        return $path;
    }

    protected function loadFile($fileName)
    {
        $fileName = $this->placeHoldersReplace($fileName);

        if (DIRECTORY_SEPARATOR == '\\') {
            $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        }

        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);

        return $this->loader->loadFile($fileName);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
}
