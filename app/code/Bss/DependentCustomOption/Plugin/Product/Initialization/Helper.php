<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\DependentCustomOption\Plugin\Product\Initialization;

use Bss\DependentCustomOption\Model\ResourceModel\DependOption;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

class Helper
{
    /**
     * @var \Bss\DependentCustomOption\Model\ResourceModel\DependOption
     */
    private $dependOption;

    /**
     * Constructor
     *
     * @param DependOption $dependOption
     */
    public function __construct(\Bss\DependentCustomOption\Model\ResourceModel\DependOption $dependOption)
    {
        $this->dependOption = $dependOption;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject
     * @param Product $product
     * @param array $productData
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws InputException
     */
    public function beforeInitializeFromData(
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject,
                                                                            $product,
                                                                            $productData
    ) {
        $validate = true;
        if (isset($productData['options'])) {
            $validate = $this->validate($productData['options']);
            $this->removeOption($productData['options'], $product);
        }
        if (!$validate) {
            throw new InputException(__('Invalid option dependent ids.'));
        }
        return [$product, $productData];
    }

    /**
     * Remove options which have other option depends on
     *
     * @param $options
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     * @throws LocalizedException
     */
    private function removeOption($options, $product)
    {
        $allDependentIds = [];
        foreach ($product->getData('options') as $productOption) {
            $allDependentIds[] = $productOption->getData('dependent_id');
        }

        $dependentIds = $this->getDependentIds($options);

        foreach ($allDependentIds as $dependentId) {
            if (!in_array($dependentId, $dependentIds)) {
                $this->dependOption->removeDependentOnOptions($dependentId);
                $this->dependOption->removeOptionByDepedentId($dependentId);
            }
        }
    }

    /**
     * @param array $options
     * @param \Magento\Catalog\Model\Product|null $product
     * @return bool
     * @throws LocalizedException
     */
    private function validate($options)
    {
        $dependentIds = $this->getDependentIds($options);
        $dependentValues = $this->getDependentValues($options);
        $diff = array_diff($dependentValues, $dependentIds);

        $same = false;
        foreach ($options as $option) {
            if (isset($option['dependent_id']) && isset($option['values'])) {
                $_dependentIds = $this->getDependentIds($option['values']);
                $_dependentIds[$option['dependent_id']] = $option['dependent_id'];
                foreach ($option['values'] as $value) {
                    if (isset($value['depend_value']) && $value['depend_value']) {
                        $depend_values = explode(',', $value['depend_value']);
                        if (!empty(array_intersect($depend_values, $_dependentIds))) {
                            $same = true;
                            break;
                        }
                    }
                }
            }
        }

        if ($same) {
            return false;
        }
        return true;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getDependentIds($options)
    {
        $val = [];
        $key = 'dependent_id';
        array_walk_recursive($options, function ($v, $k) use ($key, &$val) {
            if ($k == $key) {
                $val[$v] = $v;
            }
        });
        return $val;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getDependentValues($options)
    {
        $val = [];
        $key = 'depend_value';
        array_walk_recursive($options, function ($v, $k) use ($key, &$val) {
            if ($k == $key && $v !== '') {
                $depend_values = explode(',', $v);
                foreach ($depend_values as $value) {
                    $val[$value] = $value;
                }
            }
        });

        return $val;
    }
}
