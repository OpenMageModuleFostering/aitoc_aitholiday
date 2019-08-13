<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Notification_News extends Aitoc_Aitholiday_Model_Notification_Abstract
{
    protected $_cacheKey = 'AITOC_NEWS';
    protected $_serviceMethod = 'getNews';
}