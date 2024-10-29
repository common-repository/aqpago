<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Address
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Address implements AqpagoSerializable
{
    private $postcode;

    private $street;

    private $number;

    private $complement;

    private $district;

    private $city;

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
        $this->postcode   = isset($data->postcode) ? $data->postcode : null;
        $this->street     = isset($data->street) ? $data->street : null;
        $this->number     = isset($data->number) ? $data->number : null;
        $this->complement = isset($data->complement) ? $data->complement : null;
        $this->district   = isset($data->district) ? $data->district : null;
        $this->city       = isset($data->city) ? $data->city : null;
        $this->state      = isset($data->state) ? $data->state : null;
    }
    
    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postcode;
    }

    /**
     * @param $postcode
     *
     * @return $this
     */
    public function setPostCode($postcode)
    {
        $this->postcode = preg_replace('/[^0-9]/', '', $postcode);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param $street
     *
     * @return $this
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param $complement
     *
     * @return $this
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param $district
     *
     * @return $this
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
