<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function getAjaxUrlTemplate()
    {
        return $this->_getUrl('%module%/%controller%/%method%');
    }
    
    public function getDefaultModule()
    {
        return 'aitholiday';
    }
    
    public function getDefaultController()
    {
        return 'decoration';
    }
    
    public function getDefaultMethod()
    {
        return 'load';
    }
    
    public function getBaseUrl()
    {
        $store = Mage::app()->getStore();
        $url = str_replace(basename($_SERVER['SCRIPT_FILENAME']).'/','',$store->getBaseUrl());
        return str_replace($store->getCode().'/','',$url);
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Session
     */
    public function session()
    {
        return Mage::getSingleton('aitholiday/session');
    }
    
}