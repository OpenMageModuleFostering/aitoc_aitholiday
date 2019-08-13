<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_ManageController extends Mage_Adminhtml_Controller_Action
{
    
    protected $_forEnable = array(
        'aitholiday/manage/enable_set' => 'enable_set'
    );
    
    /**
     * 
     * @var Aitoc_Aitholiday_Model_Bridge
     */
    protected $_bridge;
    
    /**
     * Controller predispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->_isAllowed())
        {
            $this->_bridge = Mage::getModel('aitholiday/bridge');
            if ($code = $this->_getSession()->getData('aitholiday_bridge_code'))
            {
                $this->_bridge->load($code,'code');
            }
            if (!$this->_bridge->getId())
            {
                $this->_bridge->save();
            }
            $this->_getSession()->setData('aitholiday_bridge_code',$this->_bridge->getCode());
            Mage::register('aitholiday_bridge',$this->_bridge);
        }
        return $this;
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->addCss('aitoc/aitholiday/news.css');
        $block = $this->getLayout()->createBlock('aitholiday/admin_manage');

        $this->_addContent($block);

        $this->renderLayout();
    }
    
    /**
     * 
     * @return Mage_Core_Model_Config_Data
     */
    protected function _getConfigObject( $path )
    {
        return Mage::getModel('core/config_data')->load($path,'path');
    }
    
    public function saveAction()
    {
        $req = $this->getRequest();
        foreach ($this->_forEnable as $key => $field)
        {
            $this->_getConfigObject($key)->setPath($key)
            ->setValue($req->getParam($field)?"1":"0")->save();
        }
        Mage::app()->cleanCache();
        $this->_getSession()->addSuccess("Changes applied.");
        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('aitholiday');
    }
    
}