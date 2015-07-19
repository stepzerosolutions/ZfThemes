<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZfThemes for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfThemes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use ZfThemes\Manager\LayoutManager;

class ZfthemeController extends AbstractActionController
{
    /*
    * First starting point function
    * Will carry out following activities
    * 1. Create theme file structure at root -> themes folder
    * 2. Create theme Public structure at root -> public -> themes
    * 3. Create config template map file in config/autoload/local.config.php
    */
    public function indexAction()
    {
        
        $this->authService = new AuthenticationService();
        if( ! $this->authService->hasIdentity() ){
            $this->redirectAdminIndex();
        }
        $layout = $this->layout();
        $layout->setTemplate('admin/dashboard/layout');
        $themefileManager = $this->getServiceLocator()->get('themefileServices');
        $themeManager = $this->getServiceLocator()->get('templateMapService');
        $publicfilestructureManager = $this->getServiceLocator()->get('publicfilestructureManager');
        
        // This will create theme file structure
        //$themefileManager->setThemeFileStructure();
         
        //$themeManager->processTemplateMapping();
        $publicfilestructureManager->readModules();
        $publicfilestructureManager->movePublicToThemes();
        return array('output' => $themefileManager->output);
    }
    
    
    
    /*
     */
    public function layoutAction()
    {
        $this->authService = new AuthenticationService();
        if( ! $this->authService->hasIdentity() ){
            $this->redirectAdminIndex();
        }
        $layout = $this->layout();
        $layout->setTemplate('zfThemes/Zftheme/index');
        $layoutManager = new LayoutManager( $this->getServiceLocator() );
        $layoutManager->generateGlobalLayout();//var_dump( $layoutManager->output);
        return array('output' => $layoutManager->output);

    }
    
    
}
