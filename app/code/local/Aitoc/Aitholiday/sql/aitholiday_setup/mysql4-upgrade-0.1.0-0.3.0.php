<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

/* @var $this Aitoc_Aitholiday_Model_Mysql4_Setup */
$this->startSetup();

$tablePrefix = Mage::getConfig()->getTablePrefix();

$createBridge = "CREATE TABLE IF NOT EXISTS {$this->getTable('aitholiday_bridge')} (
    `code_id` INT(10) UNSIGNED NOT NULL auto_increment,
    `code` VARCHAR(32) NOT NULL ,
    `content` MEDIUMTEXT NOT NULL ,
    `date` DATETIME NOT NULL ,
    PRIMARY KEY ( `code_id` ) ,
    KEY `code` ( `code` ) ,
    KEY `date` ( `date` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$this->run($createBridge);

$this->endSetup();