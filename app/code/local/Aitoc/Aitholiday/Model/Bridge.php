<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitholiday_Model_Bridge extends Aitoc_Aitholiday_Model_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitholiday/bridge');
    }
    
    protected function _beforeSave()
    {
        if (!$this->getCode())
        {
            $this->setCode(md5(uniqid(microtime().":".mt_rand())));
        }
        if ($content = $this->getContent())
        {
            $this->setContent(serialize($content));
        }
        $this->setDate($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getId())
        {
            if ($this->getResource()->mktime($this->getDate()) < time() - 3600)
            {
                $this->delete()->setData(array())->afterLoad();
            }
        }
        return $this;
    }
    
    public function getContent()
    {
        $data = $this->getData('content');
        if (!$data)
        {
            $data = array();
        }
        elseif (is_string($data))
        {
            $data = unserialize($data);
            $this->setData('content',$data);
        }
        return $data;
    }
    
    public function getParam( $param , $default = null )
    {
        $content = $this->getContent();
        return isset($content[$param]) ? $content[$param] : $default;
    }
    
    /**
     * 
     * @param string $param
     * @param mixed $value
     * @return Aitoc_Aitholiday_Model_Bridge
     */
    public function setParam( $param , $value )
    {
        $content = $this->getContent();
        $content[$param] = $value;
        $this->setContent($content);
        return $this;
    }
    
}