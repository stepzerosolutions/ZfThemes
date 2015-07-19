<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZfThemes for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfThemes;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{

    public function onBootstrap( MvcEvent $event ){
        $app = $event->getApplication();
        $app->getEventManager()->attach('render', array($this, 'processThemeLayout'));
         
        $this->serviceManager = $app->getServiceManager();
        $sem = $app->getEventManager()->getSharedManager();
        $sem->attach( 'ZbeCore\Service\ClearCache', 'clearCacheFiles.pre', array( $this, 'processAdminCacheClean') );
    }
    
    public function processThemeLayout( MvcEvent $event ){
        //LOAD HEAD SCRIPTS
        $serviceManager = $event->getApplication()->getServiceManager();
        $sharedEvents = $event->getApplication()->getEventManager()->getSharedManager();
        $themeviewhelperFactory = $serviceManager->get('ThemeviewhelperFactory');
        $themeviewhelperFactory->processInit();
        $themeviewhelperFactory->processStyles();
    }
     
     
    /**
     * @param  MvcEvent $events
     * @return void
     */
    public function processAdminCacheClean( $events ){
    
        $cacheItems = array(
            'form_adminconfiguration_groups','headerScripts_defaultXml',
            'headerScripts_controllerActionXml_admin','sidebar_admin_configuration'
        );
        $this->serviceManager->get('cache')->removeItems( $cacheItems );
    
    
        //$events->getServiceManager()->get('cache')->removeItems($cacheItems);
    
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    
    public function getServiceConfig(){
        return array(
            'factories' => array(
                'themeServices' => 'ZfThemes\Service\themeFactory',
                'themefileServices' => 'ZfThemes\Service\themefileFactory',
                'templateMapService' => 'ZfThemes\Service\templatemapFactory',
                'publicfilestructureManager' => 'ZfThemes\Service\PublicfilestructureFactory',
                'ThemeviewhelperFactory' => 'ZfThemes\Service\ThemeviewhelperFactory',
            )
        );
    }
}
