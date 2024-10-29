<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Phones
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Phones implements AqpagoSerializable
{
    private $area;
    private $number;
    

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
        $this->area = isset($data->area) ? $data->area : null;
        $this->number = isset($data->number) ? $data->number : null;
    }
    
    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param $area
     *
     * @return $this
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->area;
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
}
