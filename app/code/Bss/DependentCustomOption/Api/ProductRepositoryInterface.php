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
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Api;

interface ProductRepositoryInterface
{
    /**
     * @param string $sku
     * @param \Bss\DependentCustomOption\Api\Data\ProductInterface $dcoProduct
     * @return \Bss\DependentCustomOption\Api\Data\ProductInterface
     */
    public function saveBySku(
        string $sku,
        \Bss\DependentCustomOption\Api\Data\ProductInterface $dcoProduct
    );

    /**
     * @param \Bss\DependentCustomOption\Api\Data\ProductInterface $dcoProduct
     * @return \Bss\DependentCustomOption\Api\Data\ProductInterface
     */
    public function save(
        \Bss\DependentCustomOption\Api\Data\ProductInterface $dcoProduct
    );

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Bss\DependentCustomOption\Api\Data\ProductSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param string $sku
     * @return \Bss\DependentCustomOption\Api\Data\ProductInterface
     */
    public function get(string $sku);
}
