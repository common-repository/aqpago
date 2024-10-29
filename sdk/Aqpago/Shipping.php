<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Shipping
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Shipping implements AqpagoSerializable
{
    private $aqenvios;

    private $amount;

    private $method;
   
    /**
     * Sale constructor.
     *
     * @param null $reference_id
     */
    public function __construct($amount = null, $method = null, $aqenvios = null)
    {
        $this->setAmount($amount);
        $this->setMethod($method);
        $this->setAqenvios($aqenvios);
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

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->aqenvios = isset($data->aqenvios) ? $data->aqenvios : null;
        $this->amount   = isset($data->amount) ? $data->amount : 0.00;
        $this->method   = isset($data->method) ? $data->method : null;
    }
    
    /**
     * @return mixed
     */
    public function getAqenvios()
    {
        return $this->araqenviosea;
    }

    /**
     * @param $aqenvios
     *
     * @return $this
     */
    public function setAqenvios($aqenvios)
    {
        $this->aqenvios = $aqenvios;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * @param $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = number_format($amount, 2, '.', '');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->amount;
    }
    
    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }
}
