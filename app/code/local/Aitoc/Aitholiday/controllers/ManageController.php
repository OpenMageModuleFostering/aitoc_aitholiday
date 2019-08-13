<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_ManageController extends Mage_Adminhtml_Controller_Action
{
    
    protected $_forEnable = array(
        'aitholiday/manage/enable_palette' => 'enable_palette' ,
        'aitholiday/manage/enable_set' => 'enable_set'
    );
    
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