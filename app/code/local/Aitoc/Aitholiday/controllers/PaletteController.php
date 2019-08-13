<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_PaletteController extends Mage_Core_Controller_Front_Action
{
    
    protected function _responseJson( $data )
    {
        $this->getResponse()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function beforeLoadAction()
    {
        $this->_adminHelper()->initBridge();
        if (!$this->_isAllowed())
        {
            return $this->_responseJson(array('status' => 0));
        }
        $storeId = $this->getRequest()->getParam('store_id');
        $id = Aitoc_Aitholiday_Model_Session::DEFAULT_PALETTE.$storeId;
        $session = $this->_adminHelper()->session();
        if ($session->checkPalette($id))
        { 
            $palette = $session->getPalette($id);
        }
        else
        {
            $palette = Mage::getModel('aitholiday/palette');
            $palette->setStoreId($storeId);
            $palette->setId($id);
            $session->registerPalette($palette);
        }
        $this->_responseJson(array(
            'status' => 1 ,
            'paletteId' => $palette->getId()
        ));
    }
    
    public function applyAction()
    {
        $this->_adminHelper()->initBridge();
        if (!$this->_isAllowed())
        {
            return;
        }
        $data = Zend_Json::decode($this->getRequest()->getParam('data'));
        $page = $this->_makePageByLocation($data['palette'],$data['location']);
        $page->addItemsByInfo($data['data']);
        $page->save();
        if ($data['location']['params'])
        {
            $page = $this->_makePageByLocation($data['palette'],$data['location'],false);
            $page->addItemsByInfo($data['data']);
            $page->save();
        }
    }
    
    public function closeAction()
    {
        $this->_adminHelper()->initBridge();
        if (!$this->_isAllowed())
        {
            return;
        }
        $this->_adminHelper()->bridge()->delete();
        $this->_adminHelper()->session()->unsAitholidayBridgeCode();
    }
    
    /**
     * 
     * @param integer $paletteId
     * @param array $location
     * @return Aitoc_Aitholiday_Model_Page
     */
    protected function _makePageByLocation( $paletteId , $location , $useUrlParams = true )
    {
        $palette = $this->_adminHelper()->session()->getPalette($paletteId);
        $page = Mage::getModel('aitholiday/page')->parseLocation($palette,$location,$useUrlParams);
        return $page;
    }
    
    public function loadAction()
    {
        $this->_adminHelper()->initBridge();
        if (!$this->_isAllowed())
        {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitholiday/admin_palette')
            ->setPaletteId($id)->toHtml()
        );
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    protected function _adminHelper()
    {
        return Mage::helper('aitholiday/admin');
    }

    protected function _isAllowed()
    {
	    return $this->_adminHelper()->bridge() && !!$this->_adminHelper()->bridge()->getId();
    }
    
}