<?php

namespace AutoLoader;

class Loader
{
    protected function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function loadFile($fileName)
    {
        $fileName = $this->normalizePath($fileName);

        if (@file_exists($fileName)) {
            include $fileName;
            return true;
        }

        return false;
    }
}
