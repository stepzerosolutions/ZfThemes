<?php
namespace ZfThemes\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfThemes\Manager\PublicfilestructureManager;

class PublicfilestructureFactory implements FactoryInterface
{
    public function createService( ServiceLocatorInterface $serviceLocator ){
        return new PublicfilestructureManager( $serviceLocator );
    }
}