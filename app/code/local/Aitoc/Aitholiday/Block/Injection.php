<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Block_Injection extends Mage_Core_Block_Template
{
    
    protected function _construct()
    {
        if ($bridge = Mage::app()->getRequest()->getParam('bridge'))
        {
            $this->_helper()->session()->setAitholidayBridgeCode($bridge);
            $this->_adminHelper()->initBridge();
        }
    }
    
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
            'palette' => $this->_helper()->session()->getAitholidayBridgeCode() ? true : false ,
            'decoration' => Mage::getStoreConfigFlag('aitholiday/manage/enable_set')
        );
        return Zend_Json::encode($config);
    }
    
    public function getLocationJSON()
    {
        $request = Mage::app()->getRequest();
        $params = $request->getParams();
        if (isset($params['bridge']))
        {
            unset($params['bridge']);
        }
        if (isset($params['___store']))
        {
            unset($params['___store']);
        }
        if (isset($params['___from_store']))
        {
            unset($params['___from_store']);
        }
        $data = array(
            'title' => addcslashes($this->getLayout()->getBlock('head')->getTitle(),"'") ,
            'module' => $request->getModuleName() ,
            'controller' => $request->getControllerName() ,
            'action' => $request->getActionName() ,
            'params' => $params
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