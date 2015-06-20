<?php
namespace ZfThemes\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfThemes\Manager\TemplatemapManager;

class TemplatemapFactory implements FactoryInterface {
    
    public function createService( ServiceLocatorInterface $serviceLocator){
        return new TemplatemapManager($serviceLocator);
    }    
}