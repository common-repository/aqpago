<?php

namespace Aqbank\Apiv2;

/**
 * Interface AqpagoEnvironment
 *
 * @package Aqbank\Apiv2
 */
interface AqpagoEnvironment
{
    /**
     * Gets the environment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl();
}
