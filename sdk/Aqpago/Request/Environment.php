<?php

namespace Aqbank\Apiv2\Aqpago\Request;

/**
 * Class Environment
 *
 * @package Aqbank\Apiv2
 */
class Environment implements \Aqbank\Apiv2\Environment
{
    private $apiUrl;

    /**
     * Environment constructor.
     *
     * @param $api
     * @param $apiQuery
     */
    private function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return Environment Sandbox
     */
    public static function sandbox()
    {
        $apiUrl = 'https://homologarapi.aqbank.com.br/api';

        return new Environment($apiUrl);
    }

    
    /**
     * @return Environment Production
     */
    public static function production()
    {
        $apiUrl = 'https://apishopaqpago.aqbank.com.br/api';

        return new Environment($apiUrl);
    }

    /**
     * Gets the environment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }
}
