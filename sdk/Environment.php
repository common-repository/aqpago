<?php

namespace Aqbank\Apiv2;

/**
 * Interface Environment
 *
 * @package Aqbank\Apiv2
 */
interface Environment
{
    /**
     * Gets the environment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl();
}
