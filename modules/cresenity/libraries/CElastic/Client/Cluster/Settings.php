<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 5:26:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Cluster settings.
 *
 * @author   Nicolas Ruflin <spam@ruflin.com>
 *
 * @link     https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-update-settings.html
 */
class CElastic_Client_Cluster_Settings {

    /**
     * @var CElastic_Client Client object
     */
    protected $_client = null;

    /**
     * Creates a cluster object.
     *
     * @param CElastic_Client $client Connection client object
     */
    public function __construct(CElastic_Client $client) {
        $this->_client = $client;
    }

    /**
     * Returns settings data.
     *
     * @return array Settings data (persistent and transient)
     */
    public function get() {
        return $this->request()->getData();
    }

    /**
     * Returns the current persistent settings of the cluster.
     *
     * If param is set, only specified setting is return.
     *
     * @param string $setting OPTIONAL Setting name to return
     *
     * @return array|string|null Settings data
     */
    public function getPersistent($setting = '') {
        $data = $this->get();
        $settings = $data['persistent'];
        if (!empty($setting)) {
            if (isset($settings[$setting])) {
                return $settings[$setting];
            }
            return;
        }
        return $settings;
    }

    /**
     * Returns the current transient settings of the cluster.
     *
     * If param is set, only specified setting is return.
     *
     * @param string $setting OPTIONAL Setting name to return
     *
     * @return array|string|null Settings data
     */
    public function getTransient($setting = '') {
        $data = $this->get();
        $settings = $data['transient'];
        if (!empty($setting)) {
            if (isset($settings[$setting])) {
                return $settings[$setting];
            }
            if (strpos($setting, '.') !== false) {
                // convert dot notation to nested arrays
                $keys = explode('.', $setting);
                foreach ($keys as $key) {
                    if (isset($settings[$key])) {
                        $settings = $settings[$key];
                    } else {
                        return;
                    }
                }
                return $settings;
            }
            return;
        }
        return $settings;
    }

    /**
     * Sets persistent setting.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Elastica\Response
     */
    public function setPersistent($key, $value) {
        return $this->set(
                        [
                            'persistent' => [
                                $key => $value,
                            ],
                        ]
        );
    }

    /**
     * Sets transient settings.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Elastica\Response
     */
    public function setTransient($key, $value) {
        return $this->set(
                        [
                            'transient' => [
                                $key => $value,
                            ],
                        ]
        );
    }

    /**
     * Sets the cluster to read only.
     *
     * Second param can be used to set it persistent
     *
     * @param bool $readOnly
     * @param bool $persistent
     *
     * @return \Elastica\Response $response
     */
    public function setReadOnly($readOnly = true, $persistent = false) {
        $key = 'cluster.blocks.read_only';
        return $persistent ? $this->setPersistent($key, $readOnly) : $this->setTransient($key, $readOnly);
    }

    /**
     * Set settings for cluster.
     *
     * @param array $settings Raw settings (including persistent or transient)
     *
     * @return \Elastica\Response
     */
    public function set(array $settings) {
        return $this->request($settings, Request::PUT);
    }

    /**
     * Get the client.
     *
     * @return \Elastica\Client
     */
    public function getClient() {
        return $this->_client;
    }

    /**
     * Sends settings request.
     *
     * @param array  $data   OPTIONAL Data array
     * @param string $method OPTIONAL Transfer method (default = CElastic_Client_Request::GET)
     *
     * @return \Elastica\Response Response object
     */
    public function request(array $data = [], $method = CElastic_Client_Request::GET) {
        $path = '_cluster/settings';
        return $this->getClient()->request($path, $method, $data);
    }

}
