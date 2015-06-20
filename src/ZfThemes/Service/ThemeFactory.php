<?php
namespace ZfThemes\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfThemes\Manager\ThemeManager;
/**
 *
 * @author Don Udugala
 *        
 */
class ThemeFactory implements FactoryInterface 
{
   public function createService( ServiceLocatorInterface $serviceLocator){
       return new ThemeManager($serviceLocator);
   } 
}