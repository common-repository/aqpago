<?php

namespace Aqbank\Apiv2\Aqpago\Request\Webhook;

use Aqbank\Apiv2\Aqpago\Request\AbstractRequest;

use Aqbank\Apiv2\Aqpago\Webhook;
use Aqbank\Apiv2\AqpagoEnvironment;
use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * Class CreateWebhookRequest
 *
 * @package Aqbank\Apiv2
 */
class CreateWebhookRequest extends AbstractRequest
{

    private $environment;

	/**
	 * CreateWebhookRequest constructor.
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
     * @param $webhook
     *
     * @return null
     * @throws \Aqbank\Apiv2\Ecommerce\Request\AqpagoRequestException
     * @throws \RuntimeException
     */
    public function execute($webhook)
    {
        $url = $this->environment->getApiUrl() . '/webhook';

        return $this->sendRequest('POST', $url, $webhook);
    }

    /**
     * @param $json
     *
     * @return Webhook
     */
    protected function unserialize($json)
    {
        return Webhook::fromJson($json);
    }
}
