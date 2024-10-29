<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Interface AqpagoSerializable
 *
 * @package Aqbank\Apiv2
 */
interface AqpagoSerializable extends \JsonSerializable
{
    /**
     * @param \stdClass $data
     *
     * @return mixed
     */
    public function populate(\stdClass $data);
}
