<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Scope extends Aitoc_Aitholiday_Model_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitholiday/scope','scope_id');
    }
    
    public function setCurrentPage( Aitoc_Aitholiday_Model_Page $page )
    {
        $this->setData('current_page',$page);
        if (!$this->hasPage())
        {
            $this->setPage($page);
        }
        return $this;
    }
    
    public function setPage( $page )
    {
        $this->setData('page',$page);
        $this->setPageId('page_id',$page->getId());
        return $this;
    }
    
    /**
     * 
     * @param $enabled
     * @return Aitoc_Aitholiday_Model_Scope
     */
    public function setEnabled( $enabled = true )
    {
        return $this->setData('enabled',$enabled);
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page');
    }
    
    public function isBase()
    {
        return $this->getIsBase();
    }
    
    public function isRelatedToPage()
    {
        return $this->getPageId() ? true : false;
    }
    
    public function isRelatedToStore()
    {
        return $this->getStoreId() ? true : false;
    }
    
    public function isCurrentPage()
    {
        return $this->getCurrentPage()->getId() == $this->getPageId();
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Mysql4_Item_Collection
     */
    public function getItems()
    {
        if (!$this->hasItems())
        {
            $collection = Mage::getResourceModel('aitholiday/item_collection')->setScopeFilter($this);
            $this->setItems($collection);
        }
        return $this->getData('items');
    }
    
    /**
     * 
     * @param $base
     * @return Aitoc_Aitholiday_Model_Scope
     */
    public function setIsBase( $base = true )
    {
        return $this->setData('is_base',$base);
    }
    
    public function canBeMain()
    {
        return $this->isBase() && $this->isRelatedToStore() && $this->isRelatedToPage() && $this->isCurrentPage();
    }
    
}