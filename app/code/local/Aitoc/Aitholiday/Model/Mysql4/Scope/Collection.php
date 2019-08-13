<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Mysql4_Scope_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    protected $_pageTable;
    
    protected $_linkTable;
    
    protected $_mainTable;
    
    protected function _construct()
    {
        $this->_init('aitholiday/scope');
        $this->_pageTable = $this->getTable('aitholiday/page');
        $this->_linkTable = $this->getTable('aitholiday/scope_page');
        $this->_mainTable = $this->getResource()->getMainTable();
    }
    
    public function setPageFilter( Aitoc_Aitholiday_Model_Page $page )
    {
        $storeId = $page->getStoreId();
        $baseSelect =  $this->getSelect()->reset();
        $select1 = $this->getResource()->getReadConnection()->select()
        ->from(array('page' => $this->_pageTable),array())
        ->joinLeft(array('lnk' => $this->_linkTable),"lnk.page_id = page.page_id",array('store_id','page_id'))
        ->joinInner(array('main_table' => $this->_mainTable),"lnk.scope_id = main_table.scope_id")
        ->where("page.page_id = ?",$page->getId())
        ->where("lnk.store_id = ? OR lnk.store_id IS NULL",$storeId);
        $select2 = $this->getResource()->getReadConnection()->select()
        ->from(array('main_table' => $this->_mainTable))
        ->joinLeft(array('lnk' => $this->_linkTable),"lnk.scope_id = main_table.scope_id",array('store_id','page_id'))
        ->where("lnk.page_id IS NULL")
        ->where("lnk.store_id = ? OR lnk.store_id IS NULL",$storeId);
        $baseSelect->union(array($select1,$select2));
    }
    
}