<?php

namespace Aqbank\Apiv2\Aqpago;

use Aqbank\Apiv2\Aqpago\Order;
use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoError;
use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoRequestException;


/**
 * Class Order
 *
 * @package Aqbank\Apiv2
 */
class UpdateOrder implements AqpagoSerializable
{
    private $order;

    /**
     * UpdateOrder constructor.
     *
     * @param null $orderId
     */
    public function __construct($orderId)
    {
        $order = new Order();
        $order->setId($orderId);
        $this->setOrder($order);
    }

    /**
     * @param $json
     *
     * @return Order
     */
    public static function fromJson($json)
    {
        $object = json_decode($json);

        $UpdateOrder = new UpdateOrder();
        $UpdateOrder->populate($object);

        return $UpdateOrder;
    }

    
    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->order = isset($data->order) ? $data->order : null;
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
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    
    /**
     * @param Order $order
     *
     * @return $this
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}

