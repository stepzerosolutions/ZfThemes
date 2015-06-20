<?php
namespace ZfThemes\Manager;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfThemes\Exception\ErrorException;
use Zend\EventManager\EventManagerInterface;
use ZfThemes\Interfaces\TemplateMapchangeInterface;
use ZfThemes\Manager\ThemefileManager;
/**
 *
 * @author Don Udugala
 * Theme functions manager
 * 
 * Save view manager template maps to config.local.php
 *
 */
class ThemeManager extends ThemefileManager 
implements ServiceManagerAwareInterface, EventManagerAwareInterface, TemplateMapchangeInterface
{
    public $serviceManager;
    private $eventManager;
    public $globalconfigFile;
    
    
    public function __construct( ServiceLocatorInterface $serviceLocator){
        $cwd = getcwd();
        parent::__construct($serviceLocator);
        $this->globalconfigFile = $cwd .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'autoload'.DIRECTORY_SEPARATOR.'config.local.php';
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getGlobalConfigurationFile(){
        return $this->globalconfigFile;
    }
    
    public function setServiceManager( ServiceManager $serviceManager){
        $this->serviceManager = $serviceManager;    
    }
    
    public function setEventManager( EventManagerInterface $eventmanager ){
        $this->eventManager = $eventmanager;
    }
    
    public function getEventManager(){
        return $this->eventManager;
    }
    /**
     * Entry point to the system
     *
     * Function checks config.local.php if not rearrange the theme variables
     * After rearrange config.local.php is saved to file system.
     * clear_config_global variable in gloal.php file control the config.local.php
     * If clear_config_global is set to true system will delete the config.local.php file very instance
     * clear_config_global must set to false in production
     * 
     */
    public function initThemeService(){
        $cwd = getcwd();
        $config = $this->serviceManager->get('config');
        if( $config["global"]["clear_config_global"] ) $this->clearGlobalConfigFile();
        if( !file_exists( $this->globalconfigFile  ) ) $this->initcurrentTheme();
        return true;
    }
    
    /**
     * Delete the config.local.php file
     * Use this function on clear cache
     *
     */
    public function clearGlobalConfigFile(){
        if( file_exists( $this->globalconfigFile  ) ) {
            unlink( $this->globalconfigFile );
        }
    }
    
    /**
     * Get global configuration details from database and save it to config.local.php
     * Set template map path to config.local.php
     *
     * Include Event trigger called initcurrentTheme.post
     * Event will trigger on begining of application bootstrap
     */
    public function initcurrentTheme(){
        if( ! $this->serviceLocator->has('ConfigurationTable') ) 
            throw new ErrorException("Coulden't found the Configuration Table Service. It should be \"ConfigurationTable\" or change the configuration table entry in ZfThemes\\Manager ThemeManager.php Line : 78, 81");
                 
        $configurationGateway = $this->serviceLocator->get('ConfigurationTable');
        $configGlobal = $configurationGateway->getByentity('configuration-global', true);
        $resultRow = [];
        if( ! is_array($configGlobal) ) $configGlobal = $this->serviceLocator->get('config')["global"];
        foreach( $configGlobal as $key => $value ){
            if( isset( $value->name ) ) {
                $resultRow[ $value->name ] = $value->value; 
            } else {
                $resultRow[ $key ] = $value; 
            }
        }
        $templateMap = $this->initTemplateMapping();
        $config = new \Zend\Config\Config( array(), true );
        $config->global = $resultRow;
        $config->view_manager = $templateMap;
        $writer = new \Zend\Config\Writer\PhpArray();
        $filename = $this->globalconfigFile;
        $writer->toFile($filename, $config);
        $config->filename = $filename;
        $this->getEventManager()->trigger( __FUNCTION__ . '.post', $this, $config );
        return true;
    }
    
    
    public function initTemplateMapping(){
        $config = $this->serviceManager->get('config');
        $viewResolver = $this->serviceLocator->get('ViewResolver');
        $themeResolver = new \Zend\View\Resolver\AggregateResolver();
        if( isset( $config["view_manager"]["template_map"] ) ){
            $templateMap = $this->setTemplateMap($config["view_manager"]["template_map"], $config["global"]);
            $viewResolverMap = $this->serviceLocator->get('ViewTemplateMapResolver');
            $viewResolverMap->add($templateMap);
            $mapresolver = new \Zend\View\Resolver\TemplateMapResolver( $templateMap );
            $themeResolver->attach($mapresolver);
        }
        $viewResolver->attach($themeResolver, 100);
        $config["view_manager"]["template_map"] = $viewResolverMap;
        return array("template_map" => $templateMap );
    }
    
    /**
     * Change themplate map array to new themes including admin
     *
     *
     * @param Array $array Template Map array
     * @param Array $globalConfig Contains global conifguration values
     * @return Array of Template map
     *
     */
    public function setTemplateMap( $templateMap, $globalConfig ){
    
        //change Application module.config.php layout/layout, application/index/index, error/404 and error/index to default theme
        foreach( $templateMap as $key => $value ){
            $templateMap[$key] = $this->getNewPath($value, $this->currentModules, $this->newThemeRoot  );
        }
        return $templateMap;
    }
    
    public function getNewPath($path, $oldModulepath, $newModulepath, $themeName='default'){
        $tmp = str_ireplace($oldModulepath, $newModulepath . DIRECTORY_SEPARATOR . $themeName . DIRECTORY_SEPARATOR .'module' . DIRECTORY_SEPARATOR, $path);
        return str_ireplace( "config/../","",$tmp );
    }
    
    public function getNewName(){
    
    }
    public function renamePath(){
    
    }
    public function getTemplateMapConfigFile(){
        return parent::getGlobalConfigurationFile();
    }
    public function processTemplateMapping(){
        parent::initThemeService();
    }
}