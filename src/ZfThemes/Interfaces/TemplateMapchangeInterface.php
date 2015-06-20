<?php
namespace ZfThemes\Interfaces;

/**
 *
 * @author wasana
 *        
 */
Interface TemplateMapchangeInterface
{
    /**
     * Returns a module path.
     *
     * @param  string $module Module name
     * @return string
     */
    public function getNewPath($path, $oldModulepath, $newModulepath);
    public function getNewName();
    public function renamePath();
    public function getTemplateMapConfigFile();
    public function clearGlobalConfigFile();
}
