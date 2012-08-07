<?php
namespace Tests\Generator\Generator;

class ModuleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $skeletonDir = 'Piotr/Generator/Resources/skeleton/';
        $generator = new \Piotr\Generator\Generator\ModuleGenerator($skeletonDir);
        
        $this->assertEquals(true, file_exists($skeletonDir));
    }
    
    public function testGenerate()
    {
        $config = new \Piotr\Generator\Command\Model\Config('Piotr', 'local');
        $config->setPath('src/');
        $config->setName('Test');
        $config->setWithControllers(true);
        $config->setWithHelper(true);
        
        $skeletonDir = 'Piotr/Generator/Resources/skeleton/module';
        $generator = new \Piotr\Generator\Generator\ModuleGenerator($skeletonDir);
        $result = $generator->generate($config);
        
        $this->assertEquals(true, $result);
    }
}