<?php

/**
 * Controller for CMS Block Widget plugin
 * @package    Buto_Addvideo
 * @author     singh1469
 */
class Buto_Addvideo_Adminhtml_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock(
            'addvideo/adminhtml_widget_chooser',
            '',
            array(
                'id' => $uniqId,
            )
        );
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}