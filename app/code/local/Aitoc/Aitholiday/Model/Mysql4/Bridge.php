<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Mysql4_Bridge extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected $_isPkAutoIncrement = false;
    
    protected function _construct()
    {
        $this->_init('aitholiday/bridge','code_id');
    }
    
}