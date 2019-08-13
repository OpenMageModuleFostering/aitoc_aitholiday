<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Block_Admin_Manage extends Mage_Adminhtml_Block_Widget
{
    
    public function __construct()
    {
        $this->setTemplate('aitholiday/manage.phtml');
        
    }
    
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
        );
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Apply'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
        return parent::_prepareLayout();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    
    public function getNews()
    {
        $news = array();
        $data = $this->_makeNews()->loadData()->getData();
        foreach ($data as $item)
        {
            $news[] = new Varien_Object($item);
        }
        return $news;
    }
    
    /**
     * 
     * @return Varien_Data_Form
     */
    public function makeForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('main',array(
            'legend' => $this->__('Module managemet')
        ));
        $fieldset->addField('enable_palette','checkbox',array(
            'name' => 'enable_palette' ,
            'value' => 1 ,
            'label' => $this->__('Enable palette:') ,
            'note' => $this->__('Enable decoration toolbar in front end for administrator') ,
            'checked' => Mage::getStoreConfigFlag('aitholiday/manage/enable_palette')
        ));
        $fieldset->addField('enable_set','checkbox',array(
            'name' => 'enable_set' ,
            'value' => 1 ,
            'label' => $this->__('Enable decorations:') ,
            'note' => $this->__('Make decorations visible to customers') ,
        	'checked' => Mage::getStoreConfigFlag('aitholiday/manage/enable_set')
        ));
        $fieldset->addField('link','note',array(
            'text' => '<a target="_blank" href="'.Mage::helper('aitholiday')->getBaseUrl().'">'.$this->__('Go to edit decorations in frontend').'</a>'
        ));
        return $form;
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Notification_News
     */
    protected function _makeNews()
    {
        return Mage::getModel('aitholiday/notification_news');
    }
    
}