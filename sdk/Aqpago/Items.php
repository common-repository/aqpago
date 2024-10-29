<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Items
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Items implements AqpagoSerializable
{
    private $name;

    private $qty;

    private $amount;
	
    private $unit_amount;

    private $image;

    private $link;

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
        $this->name         = isset($data->name) ? $data->name : null;
        $this->qty          = isset($data->qty) ? $data->qty : null;
        $this->amount  		= isset($data->amount) ? $data->amount : null;
        $this->unit_amount  = isset($data->unit_amount) ? $data->unit_amount : null;
        $this->image        = isset($data->image) ? $data->image : null;
        $this->link         = isset($data->link) ? $data->link : null;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    
    /**
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param $qty
     *
     * @return $this
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

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
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnitAmount()
    {
        return $this->unit_amount;
    }

    /**
     * @param $unit_amount
     *
     * @return $this
     */
    public function setUnitAmount($unit_amount)
    {
        $this->unit_amount = $unit_amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->unit_amount;
    }

    /**
     * @param $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }
}
