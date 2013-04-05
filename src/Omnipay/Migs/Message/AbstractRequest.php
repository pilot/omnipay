<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\Migs\Message;

/**
 * GoCardless Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $endpoint = 'https://migs.mastercard.com.au/';

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }
    
    public function getMerchantAccessCode()
    {
        return $this->getParameter('merchantAccessCode');
    }

    public function setMerchantAccessCode($value)
    {
        return $this->setParameter('merchantAccessCode', $value);
    }

    public function getSecureHash()
    {
        return $this->getParameter('secureHash');
    }

    public function setSecureHash($value)
    {
        return $this->setParameter('secureHash', $value);
    }

    protected function getBaseData()
    {
        $data = array();

        $data['vpc_Merchant']   = $this->getMerchantId();
        $data['vpc_AccessCode'] = $this->getMerchantAccessCode();
        $data['vpc_Version']    = '1';
        $data['vpc_Locale']     = 'en';
        $data['vpc_Command']    = $this->action;
        $data['vpc_Amount']      = $this->getAmount();
        $data['vpc_MerchTxnRef'] = $this->getTransactionId();
        $data['vpc_OrderInfo']   = $this->getDescription();
        $data['vpc_ReturnURL']   = $this->getReturnUrl();

        return $data;
    }
    
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    public function getHash($data)
    {
        $secureSecret = $this->getSecureHash();

        $hash = $secureSecret;

        ksort($data);

        foreach ($data as $k => $v) {
            if (substr($k, 0, 4) === 'vpc_' && $k !== 'vpc_SecureHash') {
                $hash .= $v;
            }
        }

        $hash = strtoupper(md5($hash));

        return $hash;
    }
}