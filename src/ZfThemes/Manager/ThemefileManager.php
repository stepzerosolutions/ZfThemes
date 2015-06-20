<?php
namespace ZfThemes\Manager;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Don Udugala
 * Theme functions manager
 *
 */
class ThemefileManager
implements ServiceManagerAwareInterface, EventManagerAwareInterface
{
    private $serviceManager;
    private $events;
    public $newThemeRoot;
    public $newpublicThemeRoot;
    public $currentModules;
    private $cwd;
    private $themeNames = array('default');
    public $globalconfigFile;
    public $output = array();
    /**
     */
    function __construct( ServiceLocatorInterface $serviceLocator)
    {
        $this->cwd = getcwd();
        $this->currentModules = $this->cwd . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR;
        $this->newThemeRoot = $this->cwd . DIRECTORY_SEPARATOR . 'themes';
        $this->newpublicThemeRoot = $this->cwd . DIRECTORY_SEPARATOR . 'public'. DIRECTORY_SEPARATOR. 'themes';
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\ServiceManagerAwareInterface::setServiceManager()
     *
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     *
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $eventManager;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     *
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager( new EventManager() );
        }
        return $this->events;
    }
    
    
    /**
     * Delete the config.local.php file
     * Also use this on clear cache
     *
     */
    public function clearGlobalConfigFile(){
        if( file_exists( $this->globalconfigFile  ) ) {
            unlink( $this->globalconfigFile );
        }
    }
    
   
    /**
     * Entry point to system
     *
     */
    public function setThemeFileStructure(){
        if( ! file_exists( $this->globalconfigFile) ){
            $this->scanModules();
            $this->createPublicStructure();
        }
    }
    
    /**
     * Method will create theme layout structure 
     */
    public function scanModules($themeName='default'){
       $modules = $this->serviceManager->get('modulemanager')->getModules();
       //Create theme layout file structure
       if( count($modules)>0 && !is_dir( $this->newThemeRoot ) ) {
           mkdir($this->newThemeRoot);
           array_push( $this->output, sprintf('Folder %s created.', $this->newThemeRoot ) );
       }
       
       
       if( !is_dir( $this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName ) ) {
           mkdir($this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName);
           array_push( $this->output, sprintf('Folder %s created.', $this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName ) );
       }
       
       if( is_dir($this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName) ) {
            if( !is_dir( $this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName.DIRECTORY_SEPARATOR.'module' ) ) {
                mkdir($this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName.DIRECTORY_SEPARATOR.'module');
                array_push( $this->output, sprintf('Folder %s created.', $this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName.DIRECTORY_SEPARATOR.'module') );
            }
       } 

       //Iterate the modules directories and make a copy to thems default
       foreach( $modules as $module ){
            $moduleView = $this->cwd.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'view';
            $themModule = $this->newThemeRoot.DIRECTORY_SEPARATOR.$themeName.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.$module;
            if( !is_dir($themModule) ){
               mkdir($themModule);
               array_push( $this->output, sprintf('Folder %s created.',$themModule) );
            }
            if( !is_dir($themModule.DIRECTORY_SEPARATOR.'view') ){
                mkdir($themModule.DIRECTORY_SEPARATOR.'view');
                array_push( $this->output, sprintf('Folder %s created.',$themModule.DIRECTORY_SEPARATOR.'view') );
            }
            if( is_dir($moduleView)){
                $this->directoryIterator($moduleView, $themModule.DIRECTORY_SEPARATOR.'view');
            }
       }
    }

    /**
     * Method will create public theme structure
     */
    public function createPublicStructure(){
        if( !is_dir( $this->newpublicThemeRoot ) ) {
            mkdir($this->newpublicThemeRoot);
            array_push( $this->output, sprintf('Folder %s created.', $this->newpublicThemeRoot ) );
        }
        if( is_dir( $this->newpublicThemeRoot ) ) {
            foreach( $this->themeNames as &$value ) {
                if( !is_dir( $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.$value ) ) {
                    mkdir($this->newpublicThemeRoot.DIRECTORY_SEPARATOR.$value);
                    array_push( $this->output, sprintf('Folder %s created.', $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.$value ) );
                    mkdir($this->newpublicThemeRoot.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'module');
                    array_push( $this->output, sprintf('Folder %s created.', $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'module' ) );
                }
            }
        }
    }
    
    public function directoryIterator( $dir, $themeview ){
        foreach( new \DirectoryIterator($dir) as $fileinfo ){
            if ( !$fileinfo->isDot() ) {
                if( $fileinfo->isDir() ){
                    if( !is_dir( $themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename() ) ){
                        mkdir( $themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                        array_push( $this->output, sprintf('Folder %s created.', $themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename() ) );
                    }

                    $this->directoryIterator( $dir.DIRECTORY_SEPARATOR.$fileinfo->getFilename(), $themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                }
                if( $fileinfo->isFile() ){
                   copy( $dir.DIRECTORY_SEPARATOR.$fileinfo->getFilename(),$themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename()  );
                   array_push( $this->output, sprintf('Copy %s to %s.', $dir.DIRECTORY_SEPARATOR.$fileinfo->getFilename(), $themeview.DIRECTORY_SEPARATOR.$fileinfo->getFilename()  ) );
                }
            }
        }
    }
    
}
