<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    protected $_mainTable;
    
    protected $_pageTable;
    
    /**
     * 
     * @var Aitoc_Aitholiday_Model_Page
     */
    protected $_page;
    
    protected function _construct()
    {
        $this->_init('aitholiday/item');
        $this->_mainTable = $this->getResource()->getMainTable();
        $this->_pageTable = $this->getTable('aitholiday/page');
    }
    
    protected function _afterLoad()
    {
        if ($this->_page) 
        {
            foreach ($this->_items as $item) 
            {
                $item->afterLoad();
                $item->setPage($this->_page);
            }
        }
        return parent::_afterLoad();
    }
    
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Scope $scope
     * @return Aitoc_Aitholiday_Model_Mysql4_Item_Collection
     */
    public function setScopeFilter( Aitoc_Aitholiday_Model_Scope $scope )
    {
        return $this;
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Page $page
     * @param bool $addPublic
     * @return Aitoc_Aitholiday_Model_Mysql4_Item_Collection
     */
    public function setPageFilter( Aitoc_Aitholiday_Model_Page $page , $addPublic = false )
    {
        $this->_page = $page;
        $this->getSelect()->reset();
        $this->_initSelect()->getSelect()
        ->where('page_id = ?',$page->getId())
        ->where('store_id = ?',$page->getStoreId());
        if ($addPublic)
        {
            $select1 = clone $this->getSelect();
            $this->getSelect()->reset();
            $this->_initSelect()->getSelect()
            ->where('page_id IS NULL')
            ->where('store_id = ?',$page->getStoreId());
            $select2 = clone $this->getSelect();
            $this->getSelect()->reset()->union(array($select1,$select2));
        }
        return $this;
    }
    
	/**
     * Adding item to item array
     *
     * @param   Varien_Object $item
     * @return  Varien_Data_Collection
     */
    public function addItem(Varien_Object $item)
    {
        $itemId = $this->_getItemId($item);
        if (!is_null($itemId) && isset($this->_items[$itemId])) 
        {
            $this->_items[$itemId] = $item;
            return $this;
        }
        return parent::addItem($item);
    }
    
}