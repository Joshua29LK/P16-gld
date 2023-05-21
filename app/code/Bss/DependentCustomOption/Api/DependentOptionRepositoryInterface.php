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

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

interface DependentOptionRepositoryInterface
{
    /**
     * @param int $optionId
     * @return Data\DependentOptionInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function getByOptionId(int $optionId);

    /**
     * @param string $sku
     * @return Data\DependentOptionInterface[]
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function getListBySku(string $sku);

    /**
     * @param Data\DependentOptionInterface $dependentOption
     * @return Data\DependentOptionInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws \Exception
     */
    public function save(
        \Bss\DependentCustomOption\Api\Data\DependentOptionInterface $dependentOption
    );

    /**
     * @param int $optionId
     * @param Data\DependentOptionInterface $dependentOption
     * @return Data\DependentOptionInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function saveById(
        int $optionId,
        \Bss\DependentCustomOption\Api\Data\DependentOptionInterface $dependentOption
    );
}
