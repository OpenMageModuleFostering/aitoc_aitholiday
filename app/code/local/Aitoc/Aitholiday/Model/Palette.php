<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Palette extends Mage_Core_Model_Abstract
{
    
    /**
     * 
     * @return Aitoc_Aitholiday_Helper_Admin
     */
    protected function _adminHelper()
    {
        return Mage::helper('aitholiday/admin');
    }
    
    /**
     * 
     * @return Mage_Core_Model_Design_Package
     */
    public function getDesign()
    {
        if (!$this->hasData('design'))
        {
            $store = $this->getStore();
            $design = Mage::getDesign()->setStore($store)
            ->setArea(Mage_Core_Model_Design_Package::DEFAULT_AREA);
            $this->setData('design',$design);
        }
        return $this->getData('design');
    }
    
    public function getId()
    {
        return $this->getData('id');
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Palette
     */
    public function setId( $id )
    {
        return $this->setData('id',$id);
    }
    
    /**
     *
     * @return Aitoc_Aitholiday_Model_Palette
     */
    public function setStoreId( $id )
    {
        return $this->setData('store_id',$id);
    }
    
    public function getStoreId()
    {
        return $this->getData('store_id');
    }
    
    /**
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStore( $storeId = null )
    {
        return Mage::app()->getStore(is_null($storeId)?$this->getStoreId():$storeId);
    }
    
    public function getImagesUrl()
    {
        if (!$this->hasData('images_url'))
        {
            $url = $this->getDesign()->getSkinUrl('images/aitoc/aitholiday/source');
            $this->setData('images_url',$url.'/');
        }
        return $this->getData('images_url');
    }
    
    public function getStoreBaseUrl()
    {
        return Mage::helper('aitholiday')->getBaseUrl();
    }
    
    public function getImagesPath()
    {
        if (!$this->hasData('images_path'))
        {
            $url = $this->getImagesUrl();
            $path = str_replace($this->getStoreBaseUrl(),Mage::getBaseDir().'/',$url);
            $this->setData('images_path',$path);
        }
        return $this->getData('images_path');
    }
    
    public function getBaseImagesUrl()
    {
        if (!$this->hasBaseImagesUrl())
        {
            $url = $this->getImagesUrl();
            $url = str_replace($this->getStoreBaseUrl(),'',$url);
            $this->setBaseImagesUrl($url);
        }
        return $this->getData('base_images_url');
    }
    
    /**
     * 
     * @param $filename
     * @return Aitoc_Aitholiday_Model_Image
     */
    public function getImage( $filename )
    {
        $this->getImages();
        $data = $this->getData('images');
        return isset($data[$filename]) ? $data[$filename] : null;
    }
    
    /**
     * 
     * @param $name
     * @return Aitoc_Aitholiday_Model_Image
     */
    public function getImageByName( $name )
    {
        if (!$this->hasNamedImages())
        {
            $this->getImages();
        }
        $data = $this->getNamedImages(); 
        return isset($data[$name]) ? $data[$name] : null;
    }
    
    public function getImages()
    {
        if (!$this->hasData('images'))
        {
            $namedImages = array();
            $images = array();
            $dir = new DirectoryIterator($this->getImagesPath());
            foreach ($dir as $item)
            {
                /* @var $item DirectoryIterator */
                if ($item->isFile())
                {
                    $image = Mage::getModel('aitholiday/image')->setData(array(
                        'palette' => $this ,
                        'name' => preg_replace('/\W/','_',$item->getFilename()) ,
                        'path' => $item->getPathname() ,
                        'url' => $this->getImagesUrl().$item->getFilename() ,
                        'base_url' => $this->getBaseImagesUrl().$item->getFilename(),
                        'file' => $item->getFilename()                   
                    ));
                    $images[$item->getFilename()] = $image;
                    $namedImages[$image->getName()] = $image;
                }
            }
            $this->setNamedImages($namedImages);
            $this->setData('images',$images);
        }
        return array_values($this->getData('images'));
    }
    
}