<?php
class {{ config.getNamespace }}_{{ config.getModule }}_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
            $this->loadLayout();
            $this->renderLayout();
    }
}