<?php

namespace AutoLoader;

class AutoLoader
{
    private $loader;
    private $namespaces;
    private $paths;

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

    public function loadClass($className)
    {

    }
}
