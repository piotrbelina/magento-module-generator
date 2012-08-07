<?php

namespace Piotr\Generator\Generator;

use Piotr\Generator\Command\Model\Config;

class ModuleGenerator extends Generator
{
    protected $filesystem;
    protected $skeletonDir;
    protected $routePrefix;
    protected $routeNamePrefix;
    protected $bundle;
    protected $entity;
    protected $metadata;
    protected $format;
    protected $actions;
    
    protected $config;

    /**
     * Constructor.
     *
     * @param string     $skeletonDir Path to the skeleton directory
     */
    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle           A bundle object
     * @param string            $entity           The entity relative class name
     * @param ClassMetadataInfo $metadata         The entity class metadata
     * @param string            $format           The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix      The route name prefix
     * @param array             $needWriteActions Wether or not to generate write actions
     *
     * @throws \RuntimeException
     */
    public function generate(Config $config)
    {
        $this->config = $config;
        
        $this->generateConfig();

        if ($this->config->getWithControllers()) {
            $this->generateControllers();
        }

        if ($this->config->getWithHelper()) {
            $this->generateHelper();
        }

        return true;
    }

    /**
     * Generates the routing configuration.
     *
     */
    private function generateConfig()
    {
        if ('' == $this->config->getName()) {
            throw new \InvalidArgumentException('No module name given');
        }
        $target = sprintf(
            '%s/app/code/%s/%s/%s/etc/config.xml',
            $this->config->getPath(),
            $this->config->getCodePool(),
            $this->config->getNamespace(),
            $this->config->getName()            
        );
        
        $this->renderFile($this->skeletonDir, 'config.xml', $target, array(
            'config'            => $this->config,
        ));
        
        
        $target = sprintf(
            '%s/app/etc/modules/%s_%s.xml',
            $this->config->getPath(),
            $this->config->getNamespace(),
            $this->config->getName()            
        );
        
        $this->renderFile($this->skeletonDir, 'Module.xml', $target, array(
            'config'            => $this->config,
        ));        
    }
    
    private function generateControllers()
    {
        $target = sprintf(
            '%s/app/code/%s/%s/%s/controllers/IndexController.php',
            $this->config->getPath(),
            $this->config->getCodePool(),
            $this->config->getNamespace(),
            $this->config->getName()            
        );
        
        $this->renderFile($this->skeletonDir, 'IndexController.php', $target, array(
            'config'            => $this->config,
        ));
    }
    
    private function generateHelper()
    {
        $target = sprintf(
            '%s/app/code/%s/%s/%s/Helper/Data.php',
            $this->config->getPath(),
            $this->config->getCodePool(),
            $this->config->getNamespace(),
            $this->config->getName()            
        );
        
        $this->renderFile($this->skeletonDir, 'Data.php', $target, array(
            'config'            => $this->config,
        ));
    }

    /**
     * Generates the controller class only.
     *
     */
    private function generateControllerClass()
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile($this->skeletonDir, 'controller.php', $target, array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'dir'               => $this->skeletonDir,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
        ));
    }

    /**
     * Generates the functional test class only.
     *
     */
    private function generateTestClass()
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir    = $this->bundle->getPath() .'/Tests/Controller';
        $target = $dir .'/'. str_replace('\\', '/', $entityNamespace).'/'. $entityClass .'ControllerTest.php';

        $this->renderFile($this->skeletonDir, 'tests/test.php', $target, array(
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'actions'           => $this->actions,
            'dir'               => $this->skeletonDir,
        ));
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    private function generateIndexView($dir)
    {
        $this->renderFile($this->skeletonDir, 'views/index.html.twig', $dir.'/index.html.twig', array(
            'dir'               => $this->skeletonDir,
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }

    /**
     * Generates the show.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    private function generateShowView($dir)
    {
        $this->renderFile($this->skeletonDir, 'views/show.html.twig', $dir.'/show.html.twig', array(
            'dir'               => $this->skeletonDir,
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }

    /**
     * Generates the new.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    private function generateNewView($dir)
    {
        $this->renderFile($this->skeletonDir, 'views/new.html.twig', $dir.'/new.html.twig', array(
            'dir'               => $this->skeletonDir,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'actions'           => $this->actions,
        ));
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    private function generateEditView($dir)
    {
        $this->renderFile($this->skeletonDir, 'views/edit.html.twig', $dir.'/edit.html.twig', array(
            'dir'               => $this->skeletonDir,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'actions'           => $this->actions,
        ));
    }

    /**
     * Returns an array of record actions to generate (edit, show).
     *
     * @return array
     */
    private function getRecordActions()
    {
        return array_filter($this->actions, function($item) {
            return in_array($item, array('show', 'edit'));
        });
    }
}