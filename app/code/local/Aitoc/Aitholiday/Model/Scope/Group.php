<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Scope_Group extends Aitoc_Aitholiday_Model_Abstract
{
    
    protected $_scopes = array();
    
    /**
     * 
     * @var Aitoc_Aitholiday_Model_Scope
     */
    protected $_main = null;
    
    /**
     * 
     * @param Aitoc_Aitholiday_Model_Scope $scope
     * @return Aitoc_Aitholiday_Model_Scope_Group 
     */
    public function addScope( Aitoc_Aitholiday_Model_Scope $scope )
    {
        $this->_scopes[] = $scope;
        if ($scope->canBeMain())
        {
            if ($this->_main)
            {
                throw new Aitoc_Aitholiday_Exception("On page can only be one main scope");
            }
            $this->_main = $scope;
        }
        return $this;
    }
    
    /**
     * 
     * @param $scope
     * @return Aitoc_Aitholiday_Model_Scope_Group
     */
    public function setMain( Aitoc_Aitholiday_Model_Scope $scope )
    {
        $this->_main = $scope;
        return $this;
    }
    
    /**
     * 
     * @return Aitoc_Aitholiday_Model_Scope
     */
    public function getMain()
    {
        return $this->_main;
    }
    
    
    
}