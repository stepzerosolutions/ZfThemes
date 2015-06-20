<?php
namespace ZfThemes\Manager;

use ZfThemes\Interfaces\TemplateMapchangeInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class TemplatemapManager extends ThemeManager
implements ServiceManagerAwareInterface, TemplateMapchangeInterface{
    
    public function getName($module){
    }
    
    public function getNewName(){
        
    }
    public function renamePath(){
        
    }
    public function getTemplateMapConfigFile(){
        return parent::getGlobalConfigurationFile();        
    }
    
    public function clearGlobalConfigFile(){
      if( file_exists( $this->getTemplateMapConfigFile() )){
         unlink($this->getTemplateMapConfigFile());
      }   
    }
   
    public function processTemplateMapping(){
       parent::initThemeService();
    }


    
}