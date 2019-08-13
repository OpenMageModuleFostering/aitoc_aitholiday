<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Abstract extends Mage_Core_Model_Abstract
{
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    protected function _adminHelper()
    {
        return Mage::helper('aitholiday/admin');
    }
    
}