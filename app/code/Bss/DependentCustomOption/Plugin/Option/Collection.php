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
namespace Bss\DependentCustomOption\Plugin\Option;

use Bss\DependentCustomOption\Model\DependOptionFactory;

class Collection
{
    /**
     * @var DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * Collection constructor.
     * @param DependOptionFactory $dependOptionFactory
     */
    public function __construct(
        DependOptionFactory $dependOptionFactory
    ) {
        $this->dependOptionFactory = $dependOptionFactory;
    }

    /**
     * After GetProductOptions
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductOptions(
        $subject,
        $result
    ) {
        $modelDepend = $this->dependOptionFactory->create();
        foreach ($result as $value) {
            $rowDepend = $modelDepend->loadByOptionId($value->getId());
            if (!empty($rowDepend)) {
                $value->setDependentId($rowDepend['increment_id']);
                $value->setDependValue($rowDepend['depend_value']);
            }
        }
        return $result;
    }
}
