<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_DecorationController extends Mage_Core_Controller_Front_Action
{
    
    protected $_paletteId;
    
    protected function _loadPage( $location , $useUrlParams = true )
    {
        $storeId = Mage::app()->getStore()->getId();
        $page = Mage::getModel('aitholiday/page')
        ->setPalette($this->_helper()->session()->getPalette($this->_paletteId))
        ->setUseUrlParams($useUrlParams)
        ->loadByLocation($location);
        return $page;
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('aitholiday');
    }
    
    public function preDispatch()
    {
        $result = parent::preDispatch();
        
        $storeId = Mage::app()->getStore()->getId();
        $this->_paletteId = Aitoc_Aitholiday_Model_Session::DEFAULT_PALETTE.$storeId;
        $session = $this->_helper()->session();
        if(!$session->checkPalette($this->_paletteId))
        {
            $palette = Mage::getModel('aitholiday/palette');
            $palette->setStoreId($storeId);
            $palette->setId($this->_paletteId);
            $session->registerPalette($palette);
        }
        
        return $result;
    }
    
    public function loadAction()
    {
        $location = Zend_Json::decode($this->getRequest()->getParam('location'));
        $page = $this->_loadPage($location);
        $data = array();
        $data = $page->toDecorationData();
        $canIgnoreParams = false;
        if ($location['params'])
        {
            $canIgnoreParams = true;
            $page = $this->_loadPage($location,false);
            array_splice($data,sizeof($data),0,$page->toDecorationData());
        }
        $this->_responseJson(array('items' => $data,'canIgnoreParams' => $canIgnoreParams));
    }
    
    protected function _responseJson( $data )
    {
        $this->getResponse()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
}
