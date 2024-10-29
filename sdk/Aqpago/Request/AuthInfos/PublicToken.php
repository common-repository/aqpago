<?php

namespace Aqbank\Apiv2\Aqpago\Request\AuthInfos;

use Aqbank\Apiv2\Aqpago\Request\AbstractRequest;
use Aqbank\Apiv2\AqpagoEnvironment;
use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * Class PublicToken
 *
 * @package Aqbank\Apiv2
 */
class PublicToken extends AbstractRequest
{

    private $environment;

	/**
	 * PublicToken constructor.
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
    
    public function getSellerPublicToken()
    {
        return $this->getPublicToken();
    }

    public function execute($params = null)
    {
        return [];
    }

    protected function unserialize($json)
    {
        return [];
    }
}
