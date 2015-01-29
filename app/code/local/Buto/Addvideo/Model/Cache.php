<?php

/**
 * Cache options for backend module configuration options for cache
 * Class Buto_Widget_Model_Cache
 *
 * @package    Buto_Addvideo
 * @author     singh1469
 */
class Buto_Addvideo_Model_Cache
{
    public function toOptionArray()
    {
        return array(
            array('value' => 3600, 'label' => Mage::helper('widget')->__('1 Hour')),
            array('value' => 10800, 'label' => Mage::helper('widget')->__('3 Hours')),
            array('value' => 21600, 'label' => Mage::helper('widget')->__('6 Hours')),
        );
    }

}