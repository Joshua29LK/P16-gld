<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bss\CustomizeOsc\Override\Plugin\Block\Checkout\Checkout;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Fields attribute merger.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AttributeMerger
{
    public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
    {
        if (array_key_exists('street', $result)) {
        $result['street']['children'][0]['label'] = __('Straatnaam');
        $result['street']['children'][1]['label'] = __('Huisnummer');
        $result['street']['children'][2]['label'] = __('Huisnr. toevoeging');
        }

        return $result;
    }
}
