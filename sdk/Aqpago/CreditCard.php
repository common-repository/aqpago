<?php

namespace Aqbank\Apiv2\Aqpago;

/**
 * Class CreditCard
 *
 * @package Aqbank\Apiv2
 */
class CreditCard implements \JsonSerializable, AqpagoSerializable
{
    const VISA = 'Visa';
    const MASTERCARD = 'Master';
    const AMEX = 'Amex';
    const ELO = 'Elo';
    const AURA = 'Aura';
    const JCB = 'JCB';
    const DINERS = 'Diners';
    const DISCOVER = 'Discover';
    const HIPERCARD = 'Hipercard';

    private $id;

    private $installments;

    private $card_number;

    private $holder_name;

    private $expiration_month;

    private $expiration_year;

    private $security_code;

    private $cpf;

    private $flag;

    private $first4_digits;

    private $last4_digits;

    private $type_card;

    private $card_id;

    /**
     * CreditCard constructor.
     *
     * @param null $installments
     */
    public function __construct($installments = 1)
    {
        $this->setInstallments($installments);
    }

    /**
     * @param string $json
     *
     * @return CreditCard
     */
    public static function fromJson($json)
    {
        $object = \json_decode($json);
        $card   = new CreditCard();
        $card->populate($object);

        return $card;
    }

    /**
     * @inheritdoc
     */
    public function populate(\stdClass $data)
    {
        $this->id               = isset($data->id) ? $data->id : null;
        $this->installments     = isset($data->installments) ? $data->installments : null;
        $this->card_number      = isset($data->card_number) ? $data->card_number : null;
        $this->holder_name      = isset($data->holder_name) ? $data->holder_name : null;
        $this->expiration_month = isset($data->expiration_month) ? $data->expiration_month : null;
        $this->expiration_year  = isset($data->expiration_year) ? $data->expiration_year : false;
        $this->security_code    = isset($data->security_code) ? $data->security_code : null;
        $this->cpf              = isset($data->cpf) ? $data->cpf : null;
        $this->flag             = isset($data->flag) ? $data->flag : null;
        $this->first4_digits    = isset($data->first4_digits) ? $data->first4_digits : null;
        $this->last4_digits     = isset($data->last4_digits) ? $data->last4_digits : null;
        $this->type_card        = isset($data->type_card) ? $data->type_card : null;
        $this->card_id          = isset($data->card_id) ? $data->card_id : null;
        
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
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
    public function getCardNumber()
    {
        return $this->card_number;
    }

    /**
     * @param $cardNumber
     *
     * @return $this
     */
    public function setCardNumber($card_number)
    {
        $this->card_number = $card_number;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHolderName()
    {
        return $this->holder_name;
    }

    /**
     * @param $holder
     *
     * @return $this
     */
    public function setHolderName($holder_name)
    {
        $this->holder_name = $holder_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpirationMonth()
    {
        return $this->expiration_month;
    }

    /**
     * @param $expiration_month
     *
     * @return $this
     */
    public function setExpirationMonth($expiration_month)
    {
        $this->expiration_month = str_pad($expiration_month, 2, "0", STR_PAD_LEFT);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpirationYear()
    {
        return $this->expiration_year;
    }

    /**
     * @param $expiration_year
     *
     * @return $this
     */
    public function setExpirationYear($expiration_year)
    {
        $this->expiration_year = $expiration_year;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecurityCode()
    {
        return $this->security_code;
    }

    /**
     * @param $security_code
     *
     * @return $this
     */
    public function setSecurityCode($security_code)
    {
        $this->security_code = $security_code;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param $cpf
     *
     * @return $this
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param $flag
     *
     * @return $this
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirst4Digits()
    {
        return $this->first4_digits;
    }

    /**
     * @param $first4_digits
     *
     * @return $this
     */
    public function setFirst4Digits($first4_digits)
    {
        $this->first4_digits = $first4_digits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLast4Digits()
    {
        return $this->last4_digits;
    }

    /**
     * @param $last4_digits
     *
     * @return $this
     */
    public function setLast4Digits($last4_digits)
    {
        $this->last4_digits = $last4_digits;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeCard()
    {
        return $this->type_card;
    }

    /**
     * @param string $type_card
     */
    public function setTypeCard($type_card)
    {
        $this->type_card = $type_card;

        return $this;
    }


    /**
     * @return string
     */
    public function getCardId()
    {
        return $this->card_id;
    }

    /**
     * @param string $card_id
     */
    public function setCardId($card_id)
    {
        $this->card_id = $card_id;

        return $this;
    }

}
