<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\Group;

class CustomerGroup extends Column
{
    /**
     * @var Group
     */
    protected $customerGroup;

    /**
     * CustomerGroup constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Group $customerGroup
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Group $customerGroup,
        array $components = [],
        array $data = []
    ) {
        $this->customerGroup = $customerGroup;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('customer_group');
            foreach ($dataSource['data']['items'] as &$items) {
                $groups = explode(',', $items['customer_group']);
                $customers = [];
                foreach ($groups as $key => $group) {
                    $customer = $this->customerGroup->load($group);
                    $customers[$key] =  $customer->getCustomerGroupCode();
                }
                $items['customer_group'] = implode(' - ', $customers);
            }
        }
        return $dataSource;
    }
}
