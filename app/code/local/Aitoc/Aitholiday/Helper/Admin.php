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
     * @return Aitoc_Aitholiday_Model_Bridge
     */
    public function bridge()
    {
        return Mage::registry('aitholiday_bridge');
    }
    
    /**
     * 
     * @param $code
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    public function initBridge( $code = null )
    {
        if (null === $code)
        {
            $code = $this->_publicHelper()->session()->getAitholidayBridgeCode();
        }
        if (!Mage::registry('aitholiday_bridge'))
        {
            $bridge = $this->_makeBridge()->load($code,'code');
            if ($bridge->getId())
            {
                Mage::register('aitholiday_bridge',$bridge);
            }
            else
            {
                $this->_publicHelper()->session()->unsAitholidayBridgeCode();
            }
        }
        return $this;
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Data
     */
    protected function _publicHelper()
    {
        return Mage::helper('aitholiday');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Bridge
     */
    protected function _makeBridge()
    {
        return Mage::getModel('aitholiday/bridge');
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
    
    public function getDefaultModule()
    {
        return 'aitholiday';
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