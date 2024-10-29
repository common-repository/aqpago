<?php

namespace Aqbank\Apiv2;

/**
 * Class SellerAqpago
 *
 * @package Aqbank\Apiv2
 */
class SellerAqpago implements \JsonSerializable
{
    private $tax_document;
    private $secret_access_key;
    private $user_agent;

    /**
     * SellerAqpago constructor.
     *
     * @param $document
     * @param $token
     */
    public function __construct($document, $token, $user_agent = 'Aqpago PHP SDK')
    {
        $this->tax_document         = $document;
        $this->secret_access_key    = $token;
        $this->user_agent           = $user_agent;
    }

    /**
     * Gets the seller identification document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->tax_document;
    }

    /**
     * Gets the seller identification token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->secret_access_key;
    }


    /**
     * Gets the seller identification token
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter(
            get_object_vars($this)
        );
    }
}
