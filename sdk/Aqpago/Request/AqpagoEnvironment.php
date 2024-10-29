<?php

namespace Aqbank\Apiv2\Aqpago\Request;

/**
 * Class AqpagoEnvironment
 *
 * @package Aqbank\Apiv2
 */
class AqpagoEnvironment implements \Aqbank\Apiv2\AqpagoEnvironment
{
    private $apiUrl;

    /**
     * AqpagoEnvironment constructor.
     *
     * @param $api
     * @param $apiQuery
     */
    private function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return AqpagoEnvironment Sandbox
     */
    public static function sandbox()
    {
        $apiUrl = 'https://homologarapi.aqbank.com.br/api';

        return new AqpagoEnvironment($apiUrl);
    }

    /**
     * @return AqpagoEnvironment Production
     */
    public static function production()
    {
        $apiUrl = 'https://apishopaqpago.aqbank.com.br/api';

        return new AqpagoEnvironment($apiUrl);
    }

    
    /**
     * Gets the AqpagoEnvironment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }
}
