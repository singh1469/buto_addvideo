<?php

/**
 * Render Buto Widget - frontend
 * Class Buto_Widget_Block_Chooser
 */
class Buto_Widget_Block_Chooser extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
    protected function _toHtml()
    {
        $params = $this->getData();

        if (!isset($params['video_id'])) {
            Mage::log("Invalid video id passed in");
            return; //return silently
        }

        //get embed code
        $video_id = trim((string)$params['video_id']);
        try {
            $html = Mage::getModel('widget/video')->getEmbedCode($video_id);
            return $html;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }

        return;
    }
}