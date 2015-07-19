<?php
namespace ZfThemes\Manager;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;


class PublicfilestructureManager extends ThemefileManager
implements ServiceManagerAwareInterface
{
    public $serviceManager;
    public $cwd;
    
    
    public function __construct( ServiceLocatorInterface $serviceLocator ){
        parent::__construct($serviceLocator);  
        $this->cwd = getcwd();  
        $this->serviceLocator = $serviceLocator;
    }

    public function setServiceManager( ServiceManager $serviceManager){
        $this->serviceManager = $serviceManager;
    }

    
    public function readModules(){
        $modules = $this->serviceManager->get('modulemanager')->getModules();
        // Iterate the modules directories and copy module public folder content
        // Save content to public/themes/(([a-z][A-Z][0-9])*)/module/(modulename)
        foreach( $modules as $module ){
            $modulePublic = $this->cwd.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'public';
            $saveLocation = $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.$module;
             if( file_exists($modulePublic) ) {
                $isempty = $this->is_dir_empty( $modulePublic );
                if( !is_dir($saveLocation) && !$isempty ){
                    mkdir($saveLocation);
                    array_push( $this->output, sprintf('Folder %s created.',$saveLocation) );
                }
                if( is_dir($modulePublic)){
                    $this->ThemeDirectoryIterator($modulePublic, $saveLocation.DIRECTORY_SEPARATOR);
                }
            }
        }
    }
    
    public function ThemeDirectoryIterator( $copyfrom, $copyto ){
        foreach( new \DirectoryIterator($copyfrom) as $fileinfo ){
            if ( !$fileinfo->isDot() ) {
                if( $fileinfo->isDir() ){
                $isempty = $this->is_dir_empty( $copyfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                    if( !$isempty ){
                        if( !is_dir( $copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename() ) ){
                            mkdir( $copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                            array_push( $this->output, sprintf('Folder %s created.', $copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename() ) );
                        }
                        $this->directoryIterator( $copyfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename(), $copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                    }
                }
                if( $fileinfo->isFile() ){
                    copy( $copyfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename(),$copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename()  );
                    array_push( $this->output, sprintf('Copy %s to %s.', $copyfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename(), $copyto.DIRECTORY_SEPARATOR.$fileinfo->getFilename()  ) );
                }
            }    
        }
    }
    
    public function movePublicToThemes(){
        $copfrom = $this->cwd . DIRECTORY_SEPARATOR . 'public';
        foreach( new \DirectoryIterator($copfrom) as $fileinfo ){
            if ( !$fileinfo->isDot() ) {
                if( $fileinfo->getFilename()!="themes" ){
                    if( $fileinfo->isDir() && !$this->is_dir_empty($copfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename())  ){
                        if( !file_exists($this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$fileinfo->getFilename() ) ) 
                            mkdir( $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$fileinfo->getFilename()  );
                        //die( $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$fileinfo->getFilename());
                        $this->ThemeDirectoryIterator( $copfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename(), 
                            $this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                    }
                    if( $fileinfo->isFile() ){
                        copy( $copfrom.DIRECTORY_SEPARATOR.$fileinfo->getFilename(),$this->newpublicThemeRoot.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$fileinfo->getFilename() );
                    }
                }
            }
        }
    }
    public function is_dir_empty($dir) {
        foreach( new \DirectoryIterator($dir) as $fileinfo ){
            if ( !$fileinfo->isDot() ) {
                if( $fileinfo->isFile() || $fileinfo->isDir() ){
                    return false;
                }
            }
        }
        return true;
    }
}