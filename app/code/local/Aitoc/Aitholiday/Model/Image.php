<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Image extends Mage_Core_Model_Abstract
{
    
    public function getPalette()
    {
        return $this->getData('palette');
    }
    
    public function getHtmlId()
    {
        return $this->getPalette()->getId().'_img_'.$this->getName();
    }
    
    public function getName()
    {
        return $this->getData('name');
    }
    
    public function getUrl()
    {
        return $this->getData('url');
    }
    
    public function getPath()
    {
        return $this->getData('path');
    }
    
    public function getFile()
    {
        return $this->getData('file');
    }
    
    public function getBaseUrl()
    {
        return $this->getData('base_url');
    }
    
}