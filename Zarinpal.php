<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zarinpal
{
    private $merchant_id;
    private $authority;
    private $error;
    private $ref_id;
    private $url;

    private $wsdl_url = 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
    private $pay_url = 'https://www.zarinpal.com/pg/StartPay/';

    public function __construct($params)
    {
        $this->merchant_id = $params['merchant_id'];
    }

    public function request($amount, $desc, $callback, $email = '', $mobile = '')
    {
        $params = [
            'MerchantID'  => $this->merchant_id,
            'Amount'      => $amount,
            'Description' => $desc,
            'CallbackURL' => $callback,
        ];

        if ($email) {
            $params['Email'] = $email;
        }

        if ($mobile) {
            $params['Mobile'] = $mobile;
        }

        $client = new SoapClient($this->wsdl_url, [
            'encoding' => 'UTF-8',
        ]);

        $result = $client->PaymentRequest($params);

        if ($result->Status !== 100) {
            $this->error = $result->Status;

            return false;
        }

        $this->authority = $result->Authority;
        $this->url = $this->pay_url.$this->authority;

        return true;
    }

    public function redirect()
    {
        if (!function_exists('redirect')) {
            $CI = &get_instance();
            $CI->load->helper('url');
        }

        redirect($this->url);
    }

    public function verify($amount, $authority)
    {
        $params = [
            'MerchantID' => $this->merchant_id,
            'Amount'     => $amount,
            'Authority'  => $authority,
        ];

        $client = new SoapClient($this->wsdl_url, [
            'encoding' => 'UTF-8',
        ]);

        $result = $client->PaymentVerification($params);

        if ($result->Status !== 100) {
            $this->error = $result->Status;

            return false;
        }

        $this->ref_id = $result->RefID;

        return true;
    }

    public function sandbox()
    {
        $this->wsdl_url = 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl';
        $this->pay_url = 'https://sandbox.zarinpal.com/pg/StartPay/';
    }

    public function get_authority()
    {
        return $this->authority;
    }

    public function get_error()
    {
        return $this->error;
    }

    public function get_ref_id()
    {
        return $this->ref_id;
    }
}
