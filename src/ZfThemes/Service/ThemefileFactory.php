<?php
namespace ZfThemes\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfThemes\Manager\ThemefileManager;

/**
 *
 * @author Don Udugala
 *        
 */
class ThemefileFactory implements FactoryInterface 
{
   public function createService( ServiceLocatorInterface $serviceLocator ){
       return new ThemefileManager($serviceLocator);
   } 
}