<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CustomShippingPrice\Plugin\Sales\Model\AdminOrder;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Locale\FormatInterface;
use Magento\Sales\Model\AdminOrder\Create as OrderCreate;

class Create
{
    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var FormatInterface
     */
    protected $_localeFormat;
    
    /**
     * Add constructor.
     * @param Session $authSession
     * @param FormatInterface $localeFormat
     */
    public function __construct(
        Session $authSession,
        FormatInterface $localeFormat
    ) {
        $this->_session = $authSession;
        $this->_localeFormat = $localeFormat;
    }

    /**
     * @param OrderCreate $subject
     * @return mixed
     */
    public function afterImportPostData(OrderCreate $subject, $result)
    {
        $data = $subject->getData();

        if (isset($data['shipping_amount'])) {
            $shippingPrice = $this->_parseShippingPrice(
                $data['shipping_amount']
            );
            $this->_session->setCustomshippriceAmount($shippingPrice);
        }

        if (isset($data['shipping_description'])) {
            $this->_session->setCustomshippriceDescription(
                $data['shipping_description']
            );
        }
       
        return $result;
    }

    protected function _parseShippingPrice($price)
    {
        $price = $this->_localeFormat->getNumber($price);
        return $price>0 ? $price : 0;
    }
}
