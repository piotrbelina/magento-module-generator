<?php

namespace Piotr\Generator\Command\Model;

class Config {

    protected $namespace;
    protected $codePool;
    protected $withBlock = false;
    protected $withControllers = false;
    protected $withHelper = false;
    protected $withModel = false;
    protected $withSetup = false;
    protected $active = false;
    protected $path = '';
    protected $name = '';
    
    /**
     * 
     * @param string $namespace
     * @param string $codePool
     */
    function __construct($namespace, $codePool) {
        $this->namespace = $namespace;
        $this->codePool = $codePool;
    }

    
    public function getNamespace() {
        return $this->namespace;
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getCodePool() {
        return $this->codePool;
    }

    public function setCodePool($codePool) {
        $this->codePool = $codePool;
    }

    public function getWithBlock() {
        return $this->withBlock;
    }

    public function setWithBlock($withBlock) {
        $this->withBlock = $withBlock;
    }

    public function getWithControllers() {
        return $this->withControllers;
    }

    public function setWithControllers($withControllers) {
        $this->withControllers = $withControllers;
    }

    public function getWithHelper() {
        return $this->withHelper;
    }

    public function setWithHelper($withHelper) {
        $this->withHelper = $withHelper;
    }

    public function getWithModel() {
        return $this->withModel;
    }

    public function setWithModel($withModel) {
        $this->withModel = $withModel;
    }

    public function getWithSetup() {
        return $this->withSetup;
    }

    public function setWithSetup($withSetup) {
        $this->withSetup = $withSetup;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }
    
    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }



}