<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_PRODUCT_PRICE_FORMULA
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\ProductPriceFormula\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Backend\App\ConfigInterface $backendConfig
    ) {
        $this->_backendConfig = $backendConfig;
        $this->_timezone = $timezone;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function isEnabled() {
        return (int)$this->_backendConfig->getValue('itoris_productpriceformula/general/enabled')
            && count(explode('|', $this->_backendConfig->getValue('itoris_core/installed/Itoris_ProductPriceFormula'))) == 2;
    }
    
    public function getDate($dateOrigValue) {
        // Check if $dateOrigValue is not null before using strtotime
        if ($dateOrigValue !== null) {
            return date('Y-m-d', strtotime($dateOrigValue));
        } else {
            // Handle the case where $dateOrigValue is null (e.g., return a default value)
            return 'default_date';
        }
    }
    
    public function correctDate($startDate, $endDate) {
        $start = $this->getDate($startDate);
        $end = $this->getDate($endDate);
        if (!empty($startDate) && !empty($endDate)) {
            if ($this->compareDate($start) !== 1 && $this->compareDate($end) !== -1) {
                return true;
            } else {
                return false;
            }
        } elseif (!empty($startDate) && empty($endDate)) {
            if ($this->compareDate($start) !== 1) {
                return true;
            } else {
                return false;
            }
        } elseif (empty($startDate) && !empty($endDate))  {
            if ($this->compareDate($end) !== -1) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function compareDate($date) {
        if (date('Y-m-d') == $date) return 0;
        if (date('Y-m-d') < $date) return 1;
        return -1;
    }
    
    public function customerGroup($selectedGroupId, $customerGroupId = null) {
        $allowedGroups = [];
        if (is_null($customerGroupId)) {
            $customer = $this->_objectManager->get('Magento\Customer\Model\Session');
            $customerGroupId = (int)$customer->getCustomerGroupId();
            if (!$customerGroupId) {
                $sessionQuote = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
                if ($sessionQuote) {
                    $customerId = (int)$sessionQuote->getCustomerId();
                    if ($customerId) {
                        $customer = $this->_objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface')->getById($customerId);
                        if ($customer) $customerGroupId = (int)$customer->getGroupId();
                    }
                }
            }
        }
        if (is_array($selectedGroupId)) {
            foreach ($selectedGroupId as $key => $value) {
                if ($value['group_id'] !== null) {
                    $allowedGroups[] = $value['group_id'];
                }
            }
        } else {
            $allowedGroups = $selectedGroupId !== null ? explode(',', $selectedGroupId) : [];
        }
        $allowedGroups = array_map('intval', $allowedGroups);
        if (is_null($selectedGroupId)) {
            return true;
        } else {
            if (empty($allowedGroups) || in_array($customerGroupId, $allowedGroups)) {
                return true;
            } else {
                return false;
            }
        }
    }


}