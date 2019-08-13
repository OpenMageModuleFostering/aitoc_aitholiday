<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_PaletteController extends Mage_Adminhtml_Controller_Action
{
    
	/**
     * Controller predispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->getRequest()->setQuery('ajax',true);
        Mage::getDesign()->setArea('adminhtml')
            ->setPackageName((string)Mage::getConfig()->getNode('stores/admin/design/package/name'))
            ->setTheme((string)Mage::getConfig()->getNode('stores/admin/design/theme/default'));

        $this->getLayout()->setArea('adminhtml');

        Mage::dispatchEvent('adminhtml_controller_action_predispatch_start', array());
        Mage_Core_Controller_Varien_Action::preDispatch();
        if (!$this->_isAllowed()) 
        {
            $this->_responseJson(array(
                'status' => 0
            ));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
            $this->getRequest()->setDispatched(true);
        }
    }
    
    protected function _responseJson( $data )
    {
        $this->getResponse()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function beforeLoadAction()
    {
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
	    return Mage::getSingleton('admin/session')->isAllowed('aitholiday');
    }
    
}