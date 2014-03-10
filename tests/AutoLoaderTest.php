<?php

namespace AutoLoader\test;

use AutoLoader\AutoLoader;

class AutoLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loader;

    public function setUp()
    {
        $this->loader = $this->getMock('\AutoLoader\Loader', array('loadFile'));
    }

    public function testLoadClassShouldLoadCustomPathsCorrectly()
    {
        $className = '/myProject/myPath/myFile';
        $expected = '/var/www/fakepath/fakeFile.php';
        $paths = array($className => $expected);
        $autoLoader = new AutoLoader(array(), $this->loader);
        $autoLoader->setPaths($paths);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldClassWithCorrectPaths()
    {
        $pathToProject = '/var/www';
        $expected = "{$pathToProject}/fakepath.php";
        $projectName = 'fakeProject';
        $className = "{$projectName}/fakepath";
        $autoLoader = new AutoLoader(array($projectName => $pathToProject), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldTryUseRelativePathIfNoProjectsFound()
    {
        $expected = "./fakePath.php";
        $className = "fakePath";
        $autoLoader = new AutoLoader(array(), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldTryUseRelativePathIfNoProjectsFoundAndClassHasNamespace()
    {
        $expected = "./fakeNameSpace/fakePath.php";
        $className = "\\fakeNameSpace\\fakePath";
        $autoLoader = new AutoLoader(array(), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }
}
