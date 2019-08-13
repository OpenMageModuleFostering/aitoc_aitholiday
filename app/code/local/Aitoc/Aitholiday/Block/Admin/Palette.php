<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Block_Admin_Palette extends Mage_Adminhtml_Block_Widget
{

	/**
     * 
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    protected function _adminHelper()
    {
        return Mage::helper('aitholiday/admin');
    }
    
    public function __construct()
    {
        $this->setTemplate('aitholiday/palette.phtml');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Palette
     */
    public function getPalette()
    {
        if (!$this->hasData('palette'))
        {
            $palette = $this->_adminHelper()->session()->getPalette($this->getPaletteId()); 
            $this->setData('palette',$palette);
        }
        return $this->getData('palette');
    }
    
    public function getImages()
    {
        return $this->getPalette()->getImages();
    }
    
}