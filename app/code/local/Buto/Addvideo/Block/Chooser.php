<?php

/**
 * Render Buto Widget - frontend
 * Class Buto_Widget_Block_Chooser
 *
 * @package    Buto_Addvideo
 * @author     singh1469
 */
class Buto_Addvideo_Block_Chooser extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
    protected function _toHtml()
    {
        $params = $this->getData();

        if (!isset($params['video_id'])) {
            Mage::log("Invalid video id passed in");
            return; //return silently
        }

        //get embed code
        $cache_lifetime = Mage::getStoreConfig('buto_options/buto_group/select_cache', Mage::app()->getStore());
        $video_id = trim((string)$params['video_id']);
        try {
            $html = Mage::getModel('addvideo/video')->getEmbedCode($video_id, $cache_lifetime);
            return $html;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }

        return;
    }
}