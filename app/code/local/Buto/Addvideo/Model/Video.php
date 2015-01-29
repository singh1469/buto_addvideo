<?php

/**
 * Buto video model to interface with Buto API
 * Uses Mage cache to maximise performance
 * @package    Buto_Addvideo
 * @author     singh1469
 */
class Buto_Addvideo_Model_Video extends Mage_Core_Model_Abstract
{
    const API_URL = 'https://api.buto.tv';
    const CACHE_KEY = 'buto_videos';
    const CACHE_KEY_EMBED_CODE = 'buto_embed_code';
    const CACHE_LIFETIME_DEFAULT = 3600; //one hour

    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('widget/video');
    }

    /**
     * Get all videos for this $organisation_id
     * @param $organisation_id
     * @param $api_key
     * @param $cache_lifetime
     * @return Varien_Db_Collection
     * @throws Exception
     */
    public function getAll($organisation_id, $api_key, $cache_lifetime)
    {
        if (empty($organisation_id)) {
            Mage::throwException('organisation id not passed in');
        }

        if (empty($api_key)) {
            Mage::throwException('api key not passed in');
        }

        /**
         * If user has'nt selected default cache life value, default to one hour
         */
        if (empty($cache_lifetime)) {
            $cache_lifetime = self::CACHE_LIFETIME_DEFAULT;
        }

        /**
         * Attempt to retrieve cache of buto videos
         */
        $cache = Mage::app()->getCache();
        $json = $cache->load(self::CACHE_KEY);
        if ($json === false) {
            /**
             * Get videos using buto api
             */
            $uri = self::API_URL . '/v2/video/organisation/' . $organisation_id;
            $client = new Varien_Http_Client($uri);
            $client->setAuth($api_key, 'x', Varien_Http_Client::AUTH_BASIC);
            $client->setMethod(Varien_Http_Client::GET);
            $response = $client->request();

            if (!$response->isSuccessful()) {
                Mage::throwException('error in response');
            }

            /**
             * Check if valid json data is returned
             */
            $json = $response->getBody();
            $data = json_decode($json);
            $json_error = json_last_error();
            if ($json_error) {
                Mage::throwException("buto api error - $uri request returned invalid json. $json_error");
            }
            if (count($data) <= 0) {
                Mage::throwException("buto api error - $uri request returned no video data");
            }

            /**
             * Save API json response to cache
             */
            $cache->save($json, self::CACHE_KEY, array(), $cache_lifetime);

        }

        $data = json_decode($json, true);
        $collection = new Varien_Data_Collection();
        foreach ($data as $video) {
            $object = new Varien_Object();
            $object->setData($video);
            $collection->addItem($object);
        }
        return $collection;
    }

    /**
     * Get embed code for a video via cache or curl request to buto api
     * @param $video_id
     * @param $cache_lifetime
     * @return mixed
     * @throws Exception
     */
    public function getEmbedCode($video_id, $cache_lifetime)
    {
        if (empty($video_id)) {
            Mage::throwException('video id not passed in');
        }
        /**
         * If user has'nt selected default cache life value, default to one hour
         */
        if (empty($cache_lifetime)) {
            $cache_lifetime = self::CACHE_LIFETIME_DEFAULT;
        }
        //placeholder used to create video embed template
        //this placeholder will enable us to swap in the passed in video id
        $placeholder = '!--**##**&--!';

        /**
         * Attempt to retrieve cached version of buto embed code
         */
        $cache = Mage::app()->getCache();
        $embed_template = $cache->load(self::CACHE_KEY_EMBED_CODE);
        if ($embed_template) {
            /**
             * Create html from cached embed template by swapping in $video_id for the placeholder text
             */
            $html = str_replace($placeholder, $video_id, $embed_template);
        } else {
            $uri = self::API_URL . '/v2/embed/' . $video_id;
            $client = new Varien_Http_Client($uri);
            $client->setMethod(Varien_Http_Client::GET);
            $response = $client->request();

            if (!$response->isSuccessful()) {
                Mage::throwException("error in response from buto api for request $uri");
            }

            /**
             * Convert JSON response to array
             */
            $json = $response->getBody();
            $data = json_decode($json);
            $json_error = json_last_error();
            if ($json_error !== 0) {
                Mage::throwException("error decoding json response from buto api  " . $json_error);
            }
            $html = $data[0];

            /**
             * Save API call for one hour
             * This will be used to generate future embed codes
             */
            //create embed template by swapping video id with known string
            $embed_template = str_replace($video_id, $placeholder, $html);
            $cache->save($embed_template, self::CACHE_KEY_EMBED_CODE, array(), $cache_lifetime);
        }
        return $html;
    }


    /**
     * Used in system.xml to provide module configuration options in the backend
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('widget')->__('1 hour')),
            array('value' => 2, 'label' => Mage::helper('widget')->__('3 Hours')),
            array('value' => 3, 'label' => Mage::helper('widget')->__('6 Hours')),
        );
    }
}