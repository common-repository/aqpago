<?php

namespace Aqbank\Apiv2\Aqpago\Request\Order;

use Aqbank\Apiv2\Aqpago\Request\AbstractRequest;

use Aqbank\Apiv2\Aqpago\Order;
use Aqbank\Apiv2\AqpagoEnvironment;
use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * Class CreateOrderRequest
 *
 * @package Aqbank\Apiv2
 */
class CreateOrderRequest extends AbstractRequest
{

    private $environment;

	/**
	 * CreateOrderRequest constructor.
	 *
	 * @param SellerAqpago $seller
	 * @param AqpagoEnvironment $environment
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(SellerAqpago $seller, AqpagoEnvironment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($seller, $environment, $logger);
        
        $this->environment = $environment;
    }

    /**
     * @param $order
     *
     * @return null
     * @throws \Aqbank\Apiv2\Ecommerce\Request\AqpagoRequestException
     * @throws \RuntimeException
     */
    public function execute($order)
    {
        $url = $this->environment->getApiUrl() . '/order';

        return $this->sendRequest('POST', $url, $order);
    }

    /**
     * @param $json
     *
     * @return Order
     */
    protected function unserialize($json)
    {
        return Order::fromJson($json);
    }
}
