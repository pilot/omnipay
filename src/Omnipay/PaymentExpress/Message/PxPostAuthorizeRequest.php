<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\PaymentExpress\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * PaymentExpress PxPost Authorize Request
 */
class PxPostAuthorizeRequest extends AbstractRequest
{
    protected $endpoint = 'https://sec.paymentexpress.com/pxpost.aspx';
    protected $action = 'Auth';

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    protected function getBaseData()
    {
        $data = new \SimpleXMLElement('<Txn />');
        $data->PostUsername = $this->getUsername();
        $data->PostPassword = $this->getPassword();
        $data->TxnType = $this->action;

        return $data;
    }

    public function getData()
    {
        $this->validate(array('amount', 'card'));
        $this->getCard()->validate();

        $data = $this->getBaseData();
        $data->InputCurrency = $this->getCurrency();
        $data->Amount = $this->getAmountDecimal();
        $data->MerchantReference = $this->getDescription();

        $data->CardNumber = $this->getCard()->getNumber();
        $data->CardHolderName = $this->getCard()->getName();
        $data->DateExpiry = $this->getCard()->getExpiryDate('my');
        $data->Cvc2 = $this->getCard()->getCvv();

        return $data;
    }

    public function send()
    {
        $httpResponse = $this->httpClient->post($this->endpoint, null, $this->getData()->asXML())->send();

        return $this->response = new Response($this, $httpResponse->xml());
    }
}