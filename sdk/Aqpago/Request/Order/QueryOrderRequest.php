<?php

namespace Aqbank\Apiv2\Aqpago\Request\Order;

use Aqbank\Apiv2\Aqpago\Request\AbstractRequest;
use Aqbank\Apiv2\Aqpago\Order;
use Aqbank\Apiv2\AqpagoEnvironment;
use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * Class QueryOrderRequest
 *
 * @package Aqbank\Apiv2
 */
class QueryOrderRequest extends AbstractRequest
{

    private $environment;

	/**
	 * QueryOrderRequest constructor.
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
     * @param $orderId
     *
     * @return null
     * @throws \Aqbank\Apiv2\Aqpago\Request\AqpagoRequestException
     * @throws \RuntimeException
     */
    public function execute($action)
    {
        $url = $this->environment->getApiUrl() . '/order' . $action;

        return $this->sendRequest('GET', $url);
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
