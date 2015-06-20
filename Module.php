<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZfThemes for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfThemes;

class Module
{
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
            )
        );
    }
}
