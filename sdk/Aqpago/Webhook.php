<?php

namespace Aqbank\Apiv2\Aqpago;

use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoError;
use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoRequestException;


/**
 * Class Webhook
 *
 * @package Aqbank\Apiv2
 */
class Webhook implements AqpagoSerializable
{
    private $id;
    private $public_id;
    private $url;
    private $status;
    private $created_at;
    private $updated_at;

    private $event;
    private $description;
    private $method;

    private $ccccccccccc_rows;
    private $total_pages;
    private $page;
    private $data;

    private $success;
    private $error;
    private $message;

    public function __construct()
    {

    }

    /**
     * @param $json
     *
     * @return Webhook
     */
    public static function fromJson($json)
    {
        $object = json_decode($json);

        $webhook = new Webhook();
        $webhook->populate($object);

        return $webhook;
    }

    public function populate($data)
    {
        if(is_array($data)) {
            if(count($data)) {
                foreach($data as $k => $webhook){
                   $this->data[$k] = Webhook::fromJson( json_encode($webhook) );
                }
            }
        } else {
            /** search filter **/
            if(isset($data->data)){
                $this->total_rows   = isset($data->total_rows) ? $data->total_rows : null;
                $this->total_pages  = isset($data->total_pages) ? $data->total_pages : null;
                $this->page         = isset($data->page) ? $data->page : null;
                
                if(count($data->data)) {
                    foreach($data->data as $k => $webhook){
                        $this->data[$k] = Webhook::fromJson($webhook);
                    }
                }

            } else {
                $data                   = (isset($data->webhook)) ? $data->webhook : $data;
                $this->public_id        = isset($data->public_id) ? $data->public_id : null;
                $this->url              = isset($data->url) ? $data->url : null;
                $this->status           = isset($data->status) ? $data->status : null;
                $this->created_at       = isset($data->created_at) ? $data->created_at : null;
                $this->updated_at       = isset($data->updated_at) ? $data->updated_at : null;
                $this->deleted_at       = isset($data->deleted_at) ? $data->deleted_at : null;

                $this->event            = isset($data->event) ? $data->event : null;
                $this->description      = isset($data->description) ? $data->description : null;
                $this->method           = isset($data->method) ? $data->method : null;
                $this->success          = isset($data->success) ? $data->success : null;
                $this->message          = isset($data->message) ? $data->message : null;
                $this->error            = isset($data->error) ? $data->error : null;
                
            }
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter(
            get_object_vars($this)
        );
    }
    
    /**
     * @return mixed
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /*
     *
     */
    public function setPublicId($public_id)
    {
        $this->public_id = $public_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /*
     *
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /*
     *
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /*
     *
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /*
     *
     */
    public function setDeletedAt($deleted_at)
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /*
     *
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /*
     *
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /*
     *
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /*
     *
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /*
     *
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /*
     *
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
