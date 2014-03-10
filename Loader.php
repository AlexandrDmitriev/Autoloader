<?php

namespace AutoLoader;

class Loader
{
    public function loadFile($fileName)
    {
        if (@file_exists($fileName)) {
            include $fileName;
            return true;
        }

        return false;
    }
}
