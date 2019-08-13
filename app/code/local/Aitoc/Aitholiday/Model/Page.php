<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Page extends Aitoc_Aitholiday_Model_Abstract
{
    
    protected $_isPublicItems = array();
    
    protected function _construct()
    {
        $this->_init('aitholiday/page','page_id');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Palette
     */
    public function getPalette()
    {
        return $this->getData('palette');
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Palette $palette
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function setPalette( $palette )
    {
        $this->setData('palette',$palette);
        return $this->setStoreId($palette->getStoreId());
    }
    
    /**
     * 
     * @param $use
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function setUseUrlParams( $use = true )
    {
        return $this->setData('use_url_params',$use);
    }
    
    public function isUrlParamsUsed()
    {
        return $this->getUseUrlParams();
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Palette $palette
     * @param array $location
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function parseLocation( Aitoc_Aitholiday_Model_Palette $palette , $location , $useUrlParams = true )
    {
        $this->setPalette($palette)
        ->setUseUrlParams($useUrlParams)
        ->setUrl($this->_makeUrlFromLocationData($location))
        ->setEnabled(true)->setName($location['title'])->_loadPageId();
        return $this;
    }
    
    /**
     * 
     * @param $storeId
     * @param $location
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function loadByLocation( $location )
    {
        $useUrlParams = $this->isUrlParamsUsed();
        $palette = $this->getPalette();
        $url = $this->_makeUrlFromLocationData($location);
        $this->getResource()->loadByUrl($this,$this->getStoreId(),$url)
        ->_afterLoad()->setPalette($palette)->setUseUrlParams($useUrlParams)->setOrigData();
        return $this;
    }
    
    public function hasUrlParams()
    {
        return false !== strstr($this->getUrl(),'?');
    }
    
    public function addItemsByInfo( &$info )
    {
        $this->_isPublicItems = array();
        $items = $this->getItems();
        foreach ($info as $key => $data)
        {
            #echo "\n";
            #print_r($data);
            if ($this->isUrlParamsUsed())
            {
                #echo 1;
                if (!$this->hasUrlParams() || (isset($data['is_public']) && $data['is_public']))
                {
                    #echo 2;
                    $data['use_params'] = true;
                }
                if (!isset($data['use_params']) || !$data['use_params'])
                {
                    if ($data['id'])
                    {
                        #echo 4;
                        $info[$key]['id'] = null;
                        $data['is_deleted'] = true;
                    }
                    else
                    {
                        #echo 5;
                        continue;
                    }
                }
            }
            elseif (isset($data['use_params']) && $data['use_params'])
            {
                #echo 6;
                if (isset($data['is_public']) && $data['is_public'])
                {
                    #echo 7;
                    continue;
                }
                if ($data['id'])
                {
                    #echo 8;
                    $info[$key]['id'] = null;
                    $data['is_deleted'] = true;
                }
                else
                {
                    #echo 9;
                    continue;
                }
            }
            if ($item = $this->_makeItem($data['id'])->importData($data))
            {
                if ($item->isDeleted())
                {
                    #echo " delete ";
                    $item->delete();
                    $items->removeItemByKey($item->getId());
                }
                else
                {
                    #echo " save ";
                    $items->addItem($item->save());
                    unset($info[$key]);
                }
                $this->_isPublicItems[$item->getId()] = $item->isPublic(); 
                #echo $item->isPublic() ? "p" : "l";
            }
        }
    }
    
    /**
     * 
     * @param integer $id
     * @return Aitoc_Aitholiday_Model_Item
     */
    protected function _makeItem( $id )
    {
        return Mage::getModel('aitholiday/item')->load($id)->setPage($this);
    }
    
    protected function _prepareScopes( $location )
    {
        if (!($mainScope = $this->getMainScope()))
        {
            $mainScope = $this->_createMainScope();
        }
        /* @todo continue this functional */
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Scope
     */
    protected function _makeScope()
    {
        return Mage::getModel('aitholiday/scope')->setCurrentPage($this)->setEnabled();
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Scope
     */
    protected function _createMainScope()
    {
        return $this->_makeScope()->setIsBase();
    }
    
    protected function _makeUrlFromLocationData( $location )
    {
        $url = $location['module'].'/'.$location['controller'].'/'.$location['action'];
        if ($this->isUrlParamsUsed() && $location['params'])
        {
            $params = array();
            foreach ($location['params'] as $key => $value)
            {
                if (!in_array($key,array('bridge','___store','___from_store')))
                {
                    $params[] = $key.'='.$value;
                }
            }
            $url .= $params ? '?'.join('&',$params) : '';
        }
        return $url;
    }
    
    public function getStoreId()
    {
        return $this->getData('store_id');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Mysql4_Scope_Collection
     */
    protected function getScopes()
    {
        if (!$this->hasScopes())
        {
            $collection = Mage::getResourceModel('aitholiday/scope_collection')->setPageFilter($this);
            $this->setScopes($collection);
        }
        return $this->getData('scopes');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Mysql4_Item_Collection
     */
    public function getItems()
    {
        if (!$this->hasItems())
        {
            $collection = Mage::getResourceModel('aitholiday/item_collection')->setPageFilter($this,true);
            $this->setItems($collection);
        }
        return $this->getData('items');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Scope_Group
     */
    public function getBaseScope()
    {
        if (!$this->hasBaseScope())
        {
            $baseScope = new Aitoc_Aitholiday_Model_Scope_Group();
            foreach ($this->_getScopes() as $scope)
            {
                /* @var $scope Aitoc_Aitholiday_Model_Scope */
                $scope->setCurrentPage($this);
                if ($scope->isBase())
                {
                    $baseScope->addScope($scope);
                }
            }
            $this->setBaseScope($baseScope);
        }
        return $this->getData('base_scope');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Scope
     */
    public function getMainScope()
    {
        return $this->getBaseScope()->getMain();
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Page
     */
    protected function _loadPageId()
    {
        if ($pageId = $this->getResource()->getPageId($this->getStoreId(),$this->getUrl()))
        {
            $this->setPageId($pageId);
        }
        else
        {
            $this->setIsNew();
        }
        return $this;
    }
    
    protected function _beforeSave()
    {
        return parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        foreach ($this->getItems() as $item)
        {
            $item->setPage($this);
            if ($this->isNew() && isset($this->_isPublicItems[$item->getId()]))
            {
                $item->setIsPublic($this->_isPublicItems[$item->getId()]);
            }
        }
        $this->getItems()->save();
        return $this;
    }
    
    public function setIsNew( $new = true )
    {
        return $this->setData('is_new',$new);
    }
    
    public function isNew()
    {
        return $this->getIsNew();
    }
    
    public function toDecorationData()
    {
        $result = array();
        foreach ($this->getItems() as $item)
        {
            /* @var $item Aitoc_Aitholiday_Model_Item */
            if (!$this->isUrlParamsUsed() && $item->isPublic())
            {
                continue;
            }
            if($tmp = $item->toDecorationData())
            {
                $result[] = $tmp;
            }
        }
        return $result;
    }
    
}