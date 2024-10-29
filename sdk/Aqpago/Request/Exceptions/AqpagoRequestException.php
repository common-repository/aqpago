<?php

namespace Aqbank\Apiv2\Aqpago\Request\Exceptions;

/**
 * Class AqpagoRequestException
 *
 * @package Aqbank\Apiv2
 */
class AqpagoRequestException extends \Exception
{

    private $aqpagoError;

    /**
     * AqpagoRequestException constructor.
     *
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct($message, $code, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getAqpagoError()
    {
        return $this->aqpagoError;
    }

    /**
     * @param AqpagoError $aqpagoError
     *
     * @return $this
     */
    public function setAqpagoError(AqpagoError $aqpagoError)
    {
        $this->aqpagoError = $aqpagoError;

        return $this;
    }
}
