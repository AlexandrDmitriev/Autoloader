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

    public function testAddPathsesShouldAddAliases()
    {
        $paths = array('/myProject/myPath/myFile' => '/var/www/fakepath/fakeFile.php');
        $autoLoader = new AutoLoader('', array(), $this->loader);
        $autoLoader->addPaths($paths);
        $actual = $autoLoader->paths;
        $this->assertEquals($paths, $actual);
    }

    public function testLoadClassShouldLoadCustomPathsCorrectly()
    {
        $className = '/myProject/myPath/myFile';
        $expected = '/var/www/fakepath/fakeFile.php';
        $paths = array($className => $expected);
        $autoLoader = new AutoLoader('', array(), $this->loader);
        $autoLoader->addPaths($paths);
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
        $autoLoader = new AutoLoader('', array($projectName => $pathToProject), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldTryUseRelativePathIfNoProjectsFound()
    {
        $expected = "/var/www/fakePath.php";
        $className = "fakePath";
        $autoLoader = new AutoLoader('/var/www', array(), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldTryUseRootPathIfNoProjectsFoundAndClassHasNamespace()
    {
        $expected = "/var/www/fakeNameSpace/fakePath.php";
        $className = "\\fakeNameSpace\\fakePath";
        $autoLoader = new AutoLoader('/var/www', array(), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testLoadClassShouldReplaceRootAlias()
    {
        $expected = "/var/www/fakePath.php";
        $className = "fakeNameSpace\\fakePath";
        $autoLoader = new AutoLoader('/var/www', array('fakeNameSpace'=>'{{root}}'), $this->loader);
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }

    public function testAddAliasesShouldAddAliases()
    {
        $autoLoader = new AutoLoader('', array(), $this->loader);
        $expected = array('test_alias' => '/var/www');
        $autoLoader->addAliases($expected);
        $aliases = $autoLoader->aliases;
        $this->assertEquals($expected, $aliases);
    }

    public function testLoadClassShouldReplaceAliases()
    {
        $expected = "/var/www/fakePath.php";
        $className = "fakeNameSpace\\fakePath";
        $autoLoader = new AutoLoader('/usr/lib', array('fakeNameSpace'=>'{{test_alias}}'), $this->loader);
        $autoLoader->addAliases(array('test_alias'=>'/var/www'));
        $this->loader
            ->expects($this->once())
            ->method('loadFile')
            ->with($expected)
            ->will($this->returnValue(true));
        $autoLoader->loadClass($className);
    }
}
