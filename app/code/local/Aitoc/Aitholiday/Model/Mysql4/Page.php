<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitholiday/page','page_id');
    }
    
    public function getPageId( $storeId , $url )
    {
        $select = $this->_getReadAdapter()->select()->from($this->getMainTable(),'page_id')
        ->where('url = ?',$url)->where('store_id = ?',$storeId);
        return $this->_getReadAdapter()->fetchOne($select);
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Page $page
     * @param integer $storeId
     * @param string $url
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function loadByUrl( Aitoc_Aitholiday_Model_Page $page,  $storeId , $url )
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
        ->where('url = ?',$url)->where('store_id = ?',$storeId);
        $data = $read->fetchRow($select);

        if ($data) {
            $page->setData($data);
        }

        $this->_afterLoad($page);

        return $page;
    }
    
}