<?php

/**
 * Class Buto_Widget_Block_Adminhtml_Widget_Chooser
 * Using built in Grid framework to generate widget
 */
class Buto_Widget_Block_Adminhtml_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $buto_videos = null; //collection of buto videos

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments = array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('time_created');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        /**
         * Get buto video collection and assign to class variable
         */
        try {
            $organisation_id = Mage::getStoreConfig(
                'buto_options/buto_group/input_organisation_id',
                Mage::app()->getStore()
            );
            $api_key = Mage::getStoreConfig('buto_options/buto_group/input_api_key', Mage::app()->getStore());
            $collection = Mage::getModel('buto_widget/video')->getAll($organisation_id, $api_key);
            $this->buto_videos = $collection;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->buto_videos = new Varien_Data_Collection(); //default to empty collection
        }
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/*/chooser', array('uniq_id' => $uniqId));

        //can use mage version rather than creating a copy
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        /**
         * If a video has been selected
         * Get its title to set as label
         */
        $video_id = $element->getValue();
        if ($video_id) {
            $item = $this->buto_videos->getItemByColumnValue('video_id', $video_id);
            if (isset($item)) {
                $chooser->setLabel($item->getTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var pageTitle = trElement.down("td").next().innerHTML;
                var pageId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                ' . $chooserJsObject . '.setElementValue(pageId);
                ' . $chooserJsObject . '.setElementLabel(pageTitle);
                ' . $chooserJsObject . '.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare pages collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->buto_videos);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'buto_video_id',
            array(
                'header'                    => Mage::helper('cms')->__('ID'),
                'align'                     => 'left',
                'index'                     => 'video_id',
                'filter_condition_callback' => array($this, '_idFilter'),
                'sortable'                  => false,
            )
        );

        $this->addColumn(
            'buto_title',
            array(
                'header'                    => Mage::helper('cms')->__('Title'),
                'align'                     => 'left',
                'index'                     => 'title',
                'filter_condition_callback' => array($this, '_titleFilter'),
                'sortable'                  => false,
            )
        );

        $this->addColumn(
            'buto_time_created',
            array(
                'header'   => Mage::helper('cms')->__('Created'),
                'align'    => 'left',
                'index'    => 'time_created',
                'type'     => 'datetime',
                'filter'   => false,
                'sortable' => false,
            )
        );

        $this->addColumn(
            'buto_score',
            array(
                'header'   => Mage::helper('cms')->__('Score'),
                'align'    => 'left',
                'index'    => 'score',
                'sortable' => false,
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/chooser', array('_current' => true));
    }


    /**
     * Custom callback for filtering collection by video_id
     */
    public function _idFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $filtered_collection = $this->filterCollection('video_id', $value);
        $this->setCollection($filtered_collection);

        return $this;
    }

    /**
     * Custom callback for filtering collection by title
     */
    public function _titleFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $filtered_collection = $this->filterCollection('title', $value);
        $this->setCollection($filtered_collection);

        return $this;
    }

    /**
     * Custom filtering of collection to simulate mysql 'like %xyz%' functionality
     * @param $column
     * @param $search
     * @return Varien_Data_Collection
     */
    protected function filterCollection($column, $search)
    {
        $filtered_collection = new Varien_Data_Collection();
        $search = strtolower((string)$search);
        foreach ($this->getCollection() as $item) {
            $value = strtolower((string)$item->getData($column));
            $match = (strpos($value, $search) !== false) ? true : false;
            if ($match) {
                $filtered_collection->addItem($item);
            }
        }

        return $filtered_collection;
    }

}