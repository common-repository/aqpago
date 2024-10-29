<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Customer
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Customer implements AqpagoSerializable
{
    private $name;

    private $last_name;

    private $email;

    private $tax_document;

    private $type;

    private $phones;

    private $address;

    /**
     * Customer constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->setName($name);
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
        $this->name         = isset($data->Name) ? $data->Name : null;
        $this->last_name    = isset($data->last_name) ? $data->last_name : null;
        $this->email        = isset($data->Email) ? $data->Email : null;
        $this->tax_document = isset($data->tax_document) ? $data->tax_document : null;
        $this->type         = isset($data->type) ? $data->type : null;

        if (isset($data->phones)) {
            foreach($data->phones as $k => $phone) {
                $this->phones[$k] = new Phones();
                $this->phones[$k]->populate($phone);
            }
        }
        
        if (isset($data->address)) {
            $this->address = new Address();
            $this->address->populate($data->address);
        }
    }

    /**
     * @return Address
     */
    public function address()
    {
        $address = new Address();

        $this->setAddress($address);

        return $address;
    }

    /**
     * @param     $amount
     * @param int $installments
     *
     * @return Payments
     */
    public function phones()
    {
        $phone = new Phones();

        $this->setPhones($phone);

        return $phone;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param $last_name
     *
     * @return $this
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxDocument()
    {
        return $this->tax_document;
    }

    /**
     * @param $tax_document
     *
     * @return $this
     */
    public function setTaxDocument($tax_document)
    {
        $this->tax_document = preg_replace('/[^0-9]/', '', $tax_document);

        if(strlen($this->tax_document) == 11) {
            $this->setType('F');
        } else {
            $this->setType('J');
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param $phones
     *
     * @return $this
     */
    public function setPhones($phones)
    {
        $this->phones[] = $phones;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}
