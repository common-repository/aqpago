<?php

namespace Aqbank\Apiv2\Aqpago\Request\Webhook;

use Aqbank\Apiv2\Aqpago\Request\AbstractRequest;
use Aqbank\Apiv2\Aqpago\Webhook;
use Aqbank\Apiv2\AqpagoEnvironment;
use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * Class QueryWebhooksRequest
 *
 * @package Aqbank\Apiv2
 */
class QueryWebhooksRequest extends AbstractRequest
{

    private $environment;

	/**
	 * QueryWebhooksRequest constructor.
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
    
    public function execute($params = null)
    {
        $url = $this->environment->getApiUrl() . '/webhook';

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param $json
     *
     * @return Webhooks
     */
    protected function unserialize($json)
    {
        return Webhook::fromJson($json);
    }
}
