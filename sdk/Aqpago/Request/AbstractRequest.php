<?php

namespace Aqbank\Apiv2\Aqpago\Request;

use Aqbank\Apiv2\SellerAqpago;
use Psr\Log\LoggerInterface;

use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoError;
use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoRequestException;

/**
 * Class AbstractRequest
 *
 * @package Aqbank\Apiv2
 */
abstract class AbstractRequest
{
    private $seller;
    private $environment;
    private $logger;
    private $token;
    private $auth;

    /**
     * Token public for seller
     *
     * @var string
     */
    public $pulic_token;

	/**
	 * AbstractRequest constructor.
	 *
	 * @param SellerAqpago $seller
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(SellerAqpago $seller, AqpagoEnvironment $environment, LoggerInterface $logger = null)
    {
        $this->seller       = $seller;
        $this->environment  = $environment;
        $this->logger       = $logger;

        $this->getAuth();
    }

    /**
     * @param $param
     *
     * @return mixed
     */
    public abstract function execute($param);
    
    private function getAuth()
    {
        $this->auth         = true;
        $respones           = $this->sendRequest('POST', $this->environment->getApiUrl() . '/auth/login', $this->seller); 
        $respones           = json_decode($respones, true);
        $this->token        = $respones['token'];
        $this->pulic_token  = $respones['fingerprint'];
        $this->auth         = false;
    }

    /**
     * @param                        $method
     * @param                        $url
     * @param \JsonSerializable|null $content
     *
     * @return mixed
     *
     * @throws \Aqbank\Apiv2\Aqpago\Request\AqpagoRequestException
     * @throws \RuntimeException
     */
    protected function sendRequest($method, $url, \JsonSerializable $content = null)
    {
        if(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipClient = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipClient = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ipClient = $_SERVER['REMOTE_ADDR'];
        }

        if($this->token) {
            $headers = [
                'Accept: application/json',
                'Accept-Encoding: gzip',
                'Authorization: Bearer ' . $this->token,
                'User-Agent: ' . $this->seller->getUserAgent(),
                'Seller-document: ' . $this->seller->getDocument(),
                'Seller-token: ' . $this->seller->getToken(),
                'Customer-Ip: ' . $ipClient,
                'RequestId: ' . uniqid()
            ];
        }
        else {
            $headers = [
                'Accept: application/json',
                'Accept-Encoding: gzip',
                'User-Agent: ' . $this->seller->getUserAgent(),
                'Seller-document: ' . $this->seller->getDocument(),
                'Seller-token: ' . $this->seller->getToken(),
                'Customer-Ip: ' . $ipClient,
                'RequestId: ' . uniqid()
            ];
        }
		
		
		//die($url);
		
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		
		//curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
		//curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($content !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));

            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Length: 0';
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($this->logger !== null) {
            $this->logger->debug('Request', [
                    sprintf('%s %s', $method, $url),
                    $headers,
                    json_decode(
                        preg_replace(
                            '/("card_number"):"([^"]{6})[^"]+([^"]{4})"/i', 
                            '$1:"$2******$3"', 
                            json_encode($content)
                        )
                    )
                ]
            );
        }

        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($this->logger !== null) {
            $this->logger->debug('Response', [
                sprintf('Status Code: %s', $statusCode),
                json_decode($response)
            ]);
        }

        if (curl_errno($curl)) {
            $message = sprintf('cURL error[%s]: %s', curl_errno($curl), curl_error($curl));
			
            if ($this->logger !== null) $this->logger->error($message);
			
            throw new \RuntimeException($message);
        }
		
        curl_close($curl);
       

        /** debug send */
        /* if(!$this->auth) {
            echo "<pre>";
            print_r(json_encode($headers, JSON_PRETTY_PRINT));echo "</pre>";
            echo $url . ' - ';
            echo $method;
            echo "<br/><br/>";
            print_r( $response );
            die();
        } */
        
        return $this->readResponse($statusCode, $response);
    }

    /**
     * @param $statusCode
     * @param $responseBody
     *
     * @return mixed
     *
     * @throws AqpagoRequestException
     */
    protected function readResponse($statusCode, $responseBody)
    {
        $unserialized = null;

        switch ($statusCode) {
            case 200:
            case 201:

                if($this->auth) {
                    $unserialized = $responseBody;
                }
                else {
                    $unserialized = $this->unserialize($responseBody);
                }

                break;
            case 400:
            case 401:
                $exception = null;
                $response  = json_decode($responseBody);

                $aqpagoError = new AqpagoError(json_encode($response), $statusCode);
                $exception  = new AqpagoRequestException(json_encode($response), $statusCode, $exception);
                $exception->setAqpagoError($aqpagoError);

                
                throw $exception;
            case 404:
                throw new AqpagoRequestException('Resource not found', 404, null);
            default:
                throw new AqpagoRequestException('Unknown status', $statusCode);
        }

        return $unserialized;
    }

    /**
     * @param $json
     *
     * @return mixed
     */
    protected abstract function unserialize($json);
}
