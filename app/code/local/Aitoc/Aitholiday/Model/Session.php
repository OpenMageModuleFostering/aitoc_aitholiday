<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Session extends Mage_Core_Model_Session_Abstract
{
    
    const DEFAULT_PALETTE = 'aitholiday_palette';
    
    protected $_palettes = array();
    
    public function __construct()
    {
        $this->init('aitholiday');
    }
    
    /**
     * 
     * @param string $id
     * @return Aitoc_Aitholiday_Model_Palette
     */
    public function getPalette( $id )
    {
        if (!isset($this->_palettes[$id]))
        {
            $stores = $this->getPaletteStores();
            if (!isset($stores[$id]))
            {
                throw new Aitoc_Aitholiday_Exception('Unknown palette object: '.$id);
            }
            $palette = Mage::getModel('aitholiday/palette')->setId($id)->setStoreId($stores[$id]);
            $this->_palettes[$id] = $palette;
        }
        return $this->_palettes[$id];
    }
    
    public function checkPalette($id)
    {
        if (isset($this->_palettes[$id]))
        {
            return true;
        }
        $stores = $this->getPaletteStores();
        return isset($stores[$id]);
    }
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Palette $palette
     * @return Aitoc_Aitholiday_Model_Session
     */
    public function registerPalette( Aitoc_Aitholiday_Model_Palette $palette )
    {
        $this->_palettes[$palette->getId()] = $palette;
        $stores = $this->getPaletteStores();
        $stores[$palette->getId()] = $palette->getStoreId();
        $this->setData('palette_stores',$stores);
        return $this;
    }
    
    public function getPaletteStores()
    {
        if (!$this->hasData('palette_stores'))
        {
            $this->setData('palette_stores',array());
        }
        return $this->getData('palette_stores');
    }
    
}