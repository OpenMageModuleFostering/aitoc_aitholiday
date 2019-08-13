<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Helper_Admin extends Mage_Adminhtml_Helper_Data
{
    
    /**
     * 
     * @return Mage_Admin_Model_Session
     */
    public function adminSession()
    {
        return Mage::getSingleton('admin/session');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Session
     */
    public function session()
    {
        return Mage::getSingleton('aitholiday/session');
    }
    
    public function isAvailable()
    {
        return $this->adminSession()->isAllowed('aitholiday'); 
    }
    
    public function getAjaxUrlTemplate()
    {
        return $this->getUrl('%module%/%controller%/%method%');
    }
    
    public function getDefaultModule()
    {
        return 'aitholidayadmin';
    }
    
    public function getDefaultController()
    {
        return 'palette';
    }
    
    public function getDefaultMethod()
    {
        return 'beforeLoad';
    }
    
}