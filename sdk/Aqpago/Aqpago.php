<?php

namespace Aqbank\Apiv2\Aqpago;

use Aqbank\Apiv2\Aqpago\Request\Order\CreateOrderRequest;
use Aqbank\Apiv2\Aqpago\Request\Order\QueryOrderRequest;
use Aqbank\Apiv2\Aqpago\Request\Order\UpdateOrderRequest;
use Aqbank\Apiv2\Aqpago\Request\Order\CancelOrderRequest;
use Aqbank\Apiv2\Aqpago\Request\Webhook\CreateWebhookRequest;
use Aqbank\Apiv2\Aqpago\Request\Webhook\QueryWebhooksRequest;
use Aqbank\Apiv2\Aqpago\Request\AuthInfos\PublicToken;

use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

/**
 * The Aqpago SDK;
 */
class Aqpago
{

    private $seller;

    private $environment;

    private $logger;

	/**
	 * Create an instance of Aqpago
	 *
	 * @param \Aqbank\Apiv2\SellerAqpago $seller
	 * @param \Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment $environment
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(
        \Aqbank\Apiv2\SellerAqpago $seller, 
        \Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment $environment = null, 
        LoggerInterface $logger = null
    ) {
        if ($environment == null) {
            $environment = AqpagoEnvironment::production();
        }

        $this->seller       = $seller;
        $this->environment  = $environment;
        $this->logger       = $logger;
    }
    
    /**
     * Send the Order to be created and return the Order with tid and the status
     *
     * @param Order $order
     * @return Order The Order with authorization, id, etc. returned by Aqpago.
     * 
     * @throws \Aqbank\Apiv2\Request\AqpagoRequestException
     */
    public function createOrder(Order $order)
    {
        $createOrderRequest = new CreateOrderRequest($this->seller, $this->environment, $this->logger);

        return $createOrderRequest->execute($order);
    }

    /**
     * Query a Sale on Aqpago by Id
     *
     * @param string $orderId
     * @return Aqpago The Order with authorization, id, etc. returned by Aqpago.
     * 
     * @throws \Aqbank\Apiv2\Request\AqpagoRequestException
     */
    public function getOrder($orderId)
    {
        $queryOrderRequest = new QueryOrderRequest($this->seller, $this->environment, $this->logger);
        
        return $queryOrderRequest->execute('/' . $orderId);
    }

    /**
     * Query a Sale on Aqpago by Filter
     *
     * @param string $orderId
     * @return Aqpago The Order with authorization, id, etc. returned by Aqpago.
     * 
     * @throws \Aqbank\Apiv2\Request\AqpagoRequestException
     */
    public function getSearchOrders($filters = null)
    {
        $queryOrderRequest = new QueryOrderRequest($this->seller, $this->environment, $this->logger);
        
        $request = (is_array($filters)) ? '?' . http_build_query($filters) : '';

        return $queryOrderRequest->execute( $request );
    }

    /**
     * Cancel a Order on Aqpago by orderId
     *
     * @param string  $orderId
     * @return Order The Order with authorization, id, etc. returned by Aqpago.
     *
     * @throws \Aqbank\Apiv2\Request\AqpagoRequestException
     */
    public function cancelOrder($order)
    {
        $cancelOrderRequest = new CancelOrderRequest($this->seller, $this->environment, $this->logger);
       
        return $cancelOrderRequest->execute($order);
    }

    /**
     * Update a Payment on Aqpago
     *
     * @param UpdateOrder  $order
     * @return \Aqbank\Apiv2\Payment
     *
     * @throws \Aqbank\Apiv2\Request\AqpagoRequestException
     */
    public function updateOrder(UpdateOrder $order)
    {
        $updateOrderRequest = new UpdateOrderRequest($this->seller, $this->environment, $this->logger);

        return $updateOrderRequest->execute($order);
    }

    public function getWebhooks()
    {
        $queryWebhooksRequest = new QueryWebhooksRequest($this->seller, $this->environment, $this->logger);
        
        return $queryWebhooksRequest->execute();
    }

    public function createWebhook(Webhook $webhook)
    {
        $createWebhookRequest = new CreateWebhookRequest($this->seller, $this->environment, $this->logger);

        return $createWebhookRequest->execute($webhook);
    }


    public function getPublicToken()
    {
        $publicToken = new PublicToken($this->seller, $this->environment, $this->logger);

        return $publicToken->pulic_token;
    }
}