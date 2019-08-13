<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Item extends Aitoc_Aitholiday_Model_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitholiday/item','item_id');
    }
    
    public function setStoreId( $storeId = null )
    {
        return $this->setData('store_id',$storeId);
    }
    
    public function isPublic()
    {
        return $this->getData('is_public');
    }
    
    /**
     *
     * @param array $data
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function importData( $data )
    {
        if (isset($data['is_deleted']) && $data['is_deleted'])
        {
            $this->isDeleted(true);
            return $this;
        }
        $palette = $this->getPage()->getPalette();
        /* @todo fix z-index */
        $image = $palette->getImage($data['file']);
        if (!$image)
        {
            return null;
        }
        return $this->setImage($image)
        ->setScale($data['scale'])
        ->setOffset($data['position'])
        ->setZIndex('9998')
        ->setIsPublic($data['is_public'])
        ->setIsNew($data['is_new']);
    }
    
    protected function _beforeSave()
    {
        if ($this->isPublic())
        {
            $this->setPageId(null);
        }
        return parent::_beforeSave();
    }
    
    protected function _afterLoad()
    {
        $result = parent::_afterLoad();
        if (!$this->getPageId())
        {
            $this->setIsPublic();
        }
        return $result;
    }
    
    /**
     * 
     * @param $isPublic
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setIsPublic( $isPublic = true )
    {
        $this->setData('is_public',$isPublic);
        if ($page = $this->getPage())
        {
            $this->setPage($page);
        }
        return $this;
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Page $page
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setPage( Aitoc_Aitholiday_Model_Page $page )
    {
        $this->setData('page',$page);
        if (!$this->isPublic())
        {
            $this->setPageId($page->getId());
        }
        else
        {
            $this->setPageId(null);
        }
        $this->setStoreId($page->getStoreId());
        return $this;
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Page
     */
    public function getPage()
    {
        return $this->getData('page');
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Image $image
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setImage( Aitoc_Aitholiday_Model_Image $image )
    {
        $this->setData('image',$image);
        $this->setUrl($image->getBaseUrl());
        $this->setName($image->getName());
        return $this;
    }
    
    public function getHtmlId()
    {
        return 'aitholiday_img_'.$this->getName().$this->getId();
    }
    
    /**
     * 
     * @param $scale
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setScale( $scale )
    {
        return $this->setData('scale',$scale);
    }
    
    /**
     * 
     * @param $x
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setOffsetX( $x )
    {
        return $this->setData('offset_x',$x);
    }
    
    /**
     * 
     * @param array $offset
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setOffset( array $offset )
    {
        if (isset($offset['x']) && isset($offset['y']))
        {
            extract($offset);
        }
        else
        {
            list($x,$y) = $offset;
        }
        $this->setOffsetX($x)->setOffsetY($y);
        return $this;
    }
    
    /**
     * 
     * @param $y
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setOffsetY( $y )
    {
        return $this->setData('offset_y',$y);
    }
    
    /**
     * 
     * @param $new
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setIsNew( $new = true ) 
    {
        return $this->setData('is_new',$new?true:false);
    }
    
    public function toDecorationData()
    {
        $page = $this->getPage();
        $image = $page->getPalette()->getImageByName($this->getName());
        if (!$image)
        {
            return array();
        }
        return array(
            'real_id' => $this->getId() ,
            'id' => $this->getHtmlId() ,
            'scale' => $this->getScale() ,
            'name' => $image->getName() ,
            'file' => $image->getFile() ,
            'url' => $this->getUrl() ,
            'z_index' => $this->getZIndex() ,
            'is_public' => $this->isPublic() ,
            'use_params' => $this->isPublic() || $page->isUrlParamsUsed(),
            'position' => array(
                'x' => $this->getOffsetX() ,
                'y' => $this->getOffsetY()
            )
        );
    }
    
    /**
     * 
     * @param $z
     * @return Aitoc_Aitholiday_Model_Item
     */
    public function setZIndex( $z )
    {
        return $this->setData('z_index',$z);
    }
    
}