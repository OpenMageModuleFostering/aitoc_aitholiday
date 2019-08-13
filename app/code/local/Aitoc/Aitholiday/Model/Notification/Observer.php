<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitholiday_Model_Notification_Observer extends Mage_Core_Model_Abstract
{
    /**
     * adminhtml: controller_action_predispatch
     *
     * @param Varien_Event_Observer $observer
     */
    public function performPreDispatch( Varien_Event_Observer $observer )
    {
        $news = Mage::getModel('aitholiday/notification_news');
        /* @var $news Aitoc_Aitholiday_Model_Notification_News */
        $news->loadData();
        
        $note = Mage::getModel('aitholiday/notification_notifications');
        /* @var $note Aitoc_Aitholiday_Model_Notification_Notifications */
        $note->loadData();
    }
    
}