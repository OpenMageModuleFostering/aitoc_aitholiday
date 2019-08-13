<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

/* @var $this Aitoc_Aitholiday_Model_Mysql4_Setup */
$this->startSetup();

$tablePrefix = Mage::getConfig()->getTablePrefix();

$createAitholidayPage = "CREATE TABLE IF NOT EXISTS {$this->getTable('aitholiday_page')} (
      `page_id` MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
      `store_id` SMALLINT(5) UNSIGNED,
      `parent_id` MEDIUMINT(8) UNSIGNED,
      `url` VARCHAR(255) NOT NULL,
      `enabled` BOOLEAN NOT NULL,
      `name` VARCHAR(128) NOT NULL,
      PRIMARY KEY ( `page_id` ),
      KEY `pageurl` ( `url` , `store_id` ) ,
      KEY `store` ( `store_id` ) ,
      KEY `parent` ( `parent_id` ) ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_page_store` FOREIGN KEY ( `store_id` )
      REFERENCES {$this->getTable('core_store')} ( `store_id` )
      ON DELETE CASCADE ON UPDATE CASCADE ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_page_parent` FOREIGN KEY ( `parent_id` )
      REFERENCES {$this->getTable('aitholiday_page')} ( `page_id` )
      ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$this->run($createAitholidayPage);

$this->run(
    "CREATE TABLE IF NOT EXISTS {$this->getTable('aitholiday_scope')} (
      `scope_id` MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
      `parent_id` MEDIUMINT(8) UNSIGNED,
      `enabled` BOOLEAN NOT NULL,
      `is_base` BOOLEAN NOT NULL,
      `level` TINYINT(3) UNSIGNED NOT NULL,
      `name` VARCHAR(128) NOT NULL,
      PRIMARY KEY ( `scope_id` ) ,
      KEY `parent` ( `parent_id` ) ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_scope_parent` FOREIGN KEY ( `parent_id` )
      REFERENCES {$this->getTable('aitholiday_scope')} ( `scope_id` )
      ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->run(
    "CREATE TABLE IF NOT EXISTS {$this->getTable('aitholiday_scope_page')} (
      `page_id` MEDIUMINT(8) UNSIGNED,
      `scope_id` MEDIUMINT(8) UNSIGNED NOT NULL,
      `store_id` SMALLINT(5) UNSIGNED,
      UNIQUE `group` ( `page_id` , `scope_id` ) ,
      KEY `page` ( `page_id` ) ,
      KEY `scope` ( `scope_id` ) ,
      KEY `store` ( `store_id` ) ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_scope_page_page` FOREIGN KEY ( `page_id` ) 
      REFERENCES {$this->getTable('aitholiday_page')} ( `page_id` ) 
      ON DELETE CASCADE ON UPDATE CASCADE ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_scope_page_scope` FOREIGN KEY ( `scope_id` )
      REFERENCES {$this->getTable('aitholiday_scope')} ( `scope_id` )
      ON DELETE CASCADE ON UPDATE CASCADE ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_scope_page_store` FOREIGN KEY ( `store_id` )
      REFERENCES {$this->getTable('core_store')} ( `store_id` )
      ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->run(
    "CREATE TABLE IF NOT EXISTS {$this->getTable('aitholiday_item')} (
      `item_id` INT(10) UNSIGNED NOT NULL auto_increment,
      `page_id` MEDIUMINT(8) UNSIGNED ,
      `store_id` SMALLINT(5) UNSIGNED,
      `scope_id` MEDIUMINT(8) UNSIGNED ,
      `name` VARCHAR(64) NOT NULL ,
      `url` VARCHAR(255) NOT NULL ,
      `scale` TINYINT(3) UNSIGNED NOT NULL DEFAULT 100 ,
      `offset_x` MEDIUMINT(9) NOT NULL ,
      `offset_y` MEDIUMINT(9) NOT NULL ,
      `z_index` SMALLINT(6) NOT NULL ,
      PRIMARY KEY ( `item_id` ) ,
      KEY `name` ( `name` ) ,
      KEY `page` ( `page_id` ) ,
      KEY `scope` ( `scope_id` ) ,
      KEY `store` ( `store_id` ) ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_item_page` FOREIGN KEY ( `page_id` )
      REFERENCES {$this->getTable('aitholiday_page')} ( `page_id` ) 
      ON DELETE CASCADE ON UPDATE CASCADE ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_item_scope` FOREIGN KEY ( `scope_id` )
      REFERENCES {$this->getTable('aitholiday_scope')} ( `scope_id` ) 
      ON DELETE CASCADE ON UPDATE CASCADE ,
      CONSTRAINT `{$tablePrefix}FK_aitholiday_item_store` FOREIGN KEY ( `store_id` )
      REFERENCES {$this->getTable('core_store')} ( `store_id` ) 
      ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->endSetup();