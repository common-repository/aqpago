<?php

namespace Aqbank\Apiv2\Aqpago;

use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoError;
use Aqbank\Apiv2\Aqpago\Request\Exceptions\AqpagoRequestException;

/**
 * Class Order
 *
 * @package Aqbank\Apiv2
 */
class Order implements AqpagoSerializable
{
    private $id;
    private $session_id;
    private $reference_id;
    private $platform;
    private $amount;
    private $type;
    private $status;
    private $description = 'Loja Woocommerce';
    private $date_create;
    private $aqenvios;

    private $customer;

    private $shipping;

    private $items;

    private $payments;

    private $total_rows;
    private $total_pages;
    private $page;
    private $data;

    private $success;
    private $error;
    private $message;
    
    /**
     * Order constructor.
     *
     * @param null $reference_id
     */
    public function __construct($session_id = null, $reference_id = null)
    {
        $this->setSessionId($session_id);
        $this->setReferenceId($reference_id);
    }

    /**
     * @param $json
     *
     * @return Order
     */
    public static function fromJson($json)
    {
        $object = json_decode($json);

        $order = new Order();
        $order->populate($object);

        return $order;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        /** search filter **/
        if(isset($data->data)){
            $this->total_rows   = isset($data->total_rows) ? $data->total_rows : null;
            $this->total_pages  = isset($data->total_pages) ? $data->total_pages : null;
            $this->page         = isset($data->page) ? $data->page : null;
            
            if(count($data->data)) {
                foreach($data->data as $k => $order){
                    $order = (is_object($order)) ? json_encode($order) : $order;
                    $this->data[$k] = Order::fromJson($order);
                }
            }
            
        } else {
            $data                   = (isset($data->order)) ? $data->order : $data;
            
            $this->id               = isset($data->id) ? $data->id : null;
            $this->orderId          = isset($data->id) ? $data->id : null;
            $this->reference_id     = isset($data->reference_id) ? $data->reference_id : null;
            $this->platform         = isset($data->platform) ? $data->platform : null;
            $this->amount           = isset($data->amount) ? $data->amount : null;
            $this->type             = isset($data->type) ? $data->type : null;
            $this->status           = isset($data->status) ? $data->status : null;
            $this->description      = isset($data->description) ? $data->description : null;
            $this->date_create      = isset($data->date_create) ? $data->date_create : null;
            $this->aqenvios         = isset($data->aqenvios) ? $data->aqenvios : null;
            
            $this->success          = isset($data->success) ? $data->success : null;
            $this->error            = isset($data->error) ? $data->error : null;
            $this->message          = isset($data->message) ? $data->message : null;
            
            if (isset($data->customer)) {
                $this->customer = new Customer();
                $this->customer->populate($data->customer);
            }

            
            if (isset($data->shipping)) {
                $this->shipping = new Shipping();
                $this->shipping->populate($data->shipping);
            }

            if (isset($data->items)) {
                foreach($data->items as $k => $item) {
                    $this->items[$k] = new Items();
                    $this->items[$k]->populate($item);
                }
            }

            if (isset($data->payments)) {
                foreach($data->payments as $k => $pay) {
                    $this->payments[$k] = new Payment();
                    $this->payments[$k]->populate($pay);
                }
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
     * @param $name
     *
     * @return Customer
     */
    public function customer()
    {
        $customer = new Customer();

        $this->setCustomer($customer);

        return $customer;
    }

    /**
     *
     * @return Items
     */
    public function items()
    {
        $items = new Items();

        $this->setItems($items);

        return $items;
    }

    /**
     * @param     $amount
     * @param int $installments
     *
     * @return creditCard
     */
    public function creditCard($amount, $installments, $reference_id = null, $session_id = null)
    {
        if($this->getType() == Payment::PAYMENTTYPE_TICKET) {
            throw new AqpagoRequestException('Payment for card cannot be by ticket', 400);
        }

        $payment = new Payment($amount, $installments, $reference_id, $session_id);

        $this->setPayments($payment);

        return $payment->creditCard($installments);
    }


    /**
     * @param     $amount
     * @param int $installments
     *
     * @return creditCard
     */
    public function ticket($amount, $session_id = null)
    {
        if($this->getType() == Payment::PAYMENTTYPE_CREDITCARD || $this->getType() == Payment::PAYMENTTYPE_MULTI_CRED) {
            throw new AqpagoRequestException('Payment for ticket cannot be by card', 400);
        }

        $payment = new Payment($amount, 1, null, $session_id);
        
        $this->setPayments($payment);

        return $payment->ticket();
    }

    /**
     * @param     $amount
     * @param int $installments
     *
     * @return Payments
     */
    public function shipping($amount, $method = null, $aqenvios = null)
    {
        $shipping = new Shipping($amount, $method, $aqenvios);

        $this->setShipping($shipping);

        return $shipping;
    }
    
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->orderId;
    }

    
    /**
     * @param $orderId
     *
     * @return $this
     */
    public function setId($orderId)
    {
        $this->id = $orderId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTid()
    {
        return $this->orderId;
    }

    /**
     * @param $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReferenceId()
    {
        return $this->reference_id;
    }

    /**
     * @param $reference_id
     *
     * @return $this
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param $platform
     *
     * @return $this
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = number_format($amount, 2, '.', '');

        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * @param $date_create
     *
     * @return $this
     */
    public function setDateCreate($date_create)
    {
        $this->date_create = $date_create;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAqenvios()
    {
        return $this->aqenvios;
    }

    /**
     * @param $aqenvios
     *
     * @return $this
     */
    public function setAqenvios($aqenvios)
    {
        $this->aqenvios = $aqenvios;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /*
     *
     */
    public function setShipping(Shipping $shipping)
    {
        $this->shipping = $shipping;

        return $this;
    }

    
    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /*
     * Set Items
     */
    public function setItems(Items $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /*
     *
     */
    public function setPayments(Payment $payments)
    {
        $this->payments[] = $payments;

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
    public function getError()
    {
        return $this->error;
    }

    /*
     *
     */
    public function setError($error)
    {
        $this->error = $error;

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
    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /*
     *
     */
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;

        return $this;
    }
}
