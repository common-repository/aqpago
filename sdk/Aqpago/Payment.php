<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class Payment
 *
 * @package Aqbank\Apiv2\Aqpago
 */
class Payment implements \JsonSerializable
{
    const PAYMENTTYPE_CREDITCARD    = 'credit';
    const PAYMENTTYPE_MULTI_CRED    = 'multi_credit';
    const PAYMENTTYPE_TICKET        = 'ticket';
    const PAYMENTTYPE_MULTI_TICKET  = 'multi_ticket';
    
    private $id;

    private $reference_id;

    private $installments;

    private $amount;

    private $type;

    private $status;

    private $credit_card;

    private $body_instructions;

    private $email;

    private $payment_multi;

    private $message;

    private $created_at;

    private $payment_date;

    private $ticket_url;

    private $ticket_bar_code;

    private $expiration_date;

    /**
     * Payment constructor.
     *
     * @param int $amount
     * @param int $installments
     */
    public function __construct($amount = 0, $installments = 1, $reference_id = null)
    {
        $this->setAmount($amount);
        $this->setInstallments($installments);
        $this->setReferenceId($reference_id);
    }

    /**
     * @param $json
     *
     * @return Payment
     */
    public static function fromJson($json)
    {
        $payment = new Payment();
        $payment->populate(json_decode($json));

        return $payment;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->id               = isset($data->id) ? $data->id : null;
        $this->reference_id     = isset($data->reference_id) ? $data->reference_id : null;
        $this->installments     = isset($data->installments) ? $data->installments : null;
        $this->amount           = isset($data->amount) ? $data->amount : null;
        $this->type             = isset($data->type) ? $data->type : false;
       
        $this->payment_multi    = isset($data->payment_multi) ? $data->payment_multi : false;
        $this->status           = isset($data->status) ? $data->status : false;
        $this->message          = isset($data->message) ? $data->message : false;
        $this->created_at       = isset($data->created_at) ? $data->created_at : false;
        $this->payment_date     = isset($data->payment_date) ? $data->payment_date : false;

        $this->ticket_url       = isset($data->ticket_url) ? $data->ticket_url : false;
        $this->ticket_bar_code  = isset($data->ticket_bar_code) ? $data->ticket_bar_code : false;
        $this->expiration_date  = isset($data->expiration_date) ? $data->expiration_date : false;

        if (isset($data->credit_card)) {
            $this->credit_card = new CreditCard();
            $this->credit_card->populate($data->credit_card);
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
     *
     * @return CreditCard
     */
    public function CreditCard($installments)
    {
        $card = new CreditCard($installments);

        $this->setType(self::PAYMENTTYPE_CREDITCARD);
        $this->setCreditCard($card);

        return $card;
    }


    /**
     *
     * @return CreditCard
     */
    public function ticket()
    {
        $this->setType(self::PAYMENTTYPE_TICKET);

        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $reference_id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
    public function getInstallments()
    {
        return $this->installments;
    }

    /**
     * @param $installments
     *
     * @return $this
     */
    public function setInstallments($installments)
    {
        $this->installments = $installments;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreditCard()
    {
        return $this->credit_card;
    }

    /**
     * @param CreditCard $creditCard
     *
     * @return $this
     */
    public function setCreditCard(CreditCard $creditCard)
    {
        $this->credit_card = $creditCard;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setPaymentId($id)
    {
        $this->id = $id;

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
    public function getBodyInstructions()
    {
        return $this->body_instructions;
    }

    /**
     * @param $body_instructions
     *
     * @return $this
     */
    public function setBodyInstructions($body_instructions)
    {
        $this->body_instructions = $body_instructions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentMulti()
    {
        return $this->payment_multi;
    }

    /**
     * @param $payment_multi
     *
     * @return $this
     */
    public function setPaymentMulti($payment_multi)
    {
        $this->payment_multi = $payment_multi;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param $created_at
     *
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * @param $payment_date
     *
     * @return $this
     */
    public function setPaymentDate($payment_date)
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTicketUrl()
    {
        return $this->ticket_url;
    }

    /**
     * @param $ticket_url
     *
     * @return $this
     */
    public function setTicketUrl($ticket_url)
    {
        $this->ticket_url = $ticket_url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTicketBarCode()
    {
        return $this->ticket_bar_code;
    }

    /**
     * @param $ticket_bar_code
     *
     * @return $this
     */
    public function setTicketBarCode($ticket_bar_code)
    {
        $this->ticket_bar_code = $ticket_bar_code;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * @param $expiration_date
     *
     * @return $this
     */
    public function setExpirationDate($expiration_date)
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }
}
