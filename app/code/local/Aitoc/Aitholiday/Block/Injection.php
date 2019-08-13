<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Block_Injection extends Mage_Core_Block_Template
{
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    protected function _adminHelper()
    {
        return $this->helper('aitholiday/admin');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Data
     */
    protected function _helper()
    {
        return $this->helper('aitholiday');
    }
    
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }
    
    public function getConfigJSON()
    {
        $config = array(
            'palette' => Mage::getStoreConfigFlag('aitholiday/manage/enable_palette') ,
            'decoration' => Mage::getStoreConfigFlag('aitholiday/manage/enable_set')
        );
        return Zend_Json::encode($config);
    }
    
    public function getLocationJSON()
    {
        $request = Mage::app()->getRequest();
        $data = array(
            'title' => $this->getLayout()->getBlock('head')->getTitle() ,
            'module' => $request->getModuleName() ,
            'controller' => $request->getControllerName() ,
            'action' => $request->getActionName() ,
            'params' => $request->getParams()
        );
        return Zend_Json::encode($data);
    }
    
    public function getTranlsationJSON()
    {
        return Zend_Json::encode(array(
            'public' => $this->__('On all pages:') ,
            'ignore_url_params' => $this->__('On similar pages:') 
        ));
    }
    
}