<?php

namespace AutoLoader;

class Loader
{
    public function loadFile($fileName)
    {
        include $fileName;
        return true;
    }
}
