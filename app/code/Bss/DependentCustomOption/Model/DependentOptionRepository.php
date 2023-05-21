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

namespace Bss\DependentCustomOption\Model;

use Bss\DependentCustomOption\Api\Data\DependentOptionInterface;
use Bss\DependentCustomOption\Api\DependentOptionRepositoryInterface;
use Bss\DependentCustomOption\Model\CustomOptions\DcoProcessor;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\InputException;

class DependentOptionRepository implements DependentOptionRepositoryInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DcoProcessor
     */
    protected $dcoProcessor;

    /**
     * DependentOptionRepository constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param DcoProcessor $dcoProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DcoProcessor $dcoProcessor
    ) {
        $this->productRepository = $productRepository;
        $this->dcoProcessor = $dcoProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getListBySku(string $sku)
    {
        $product = $this->productRepository->get($sku);
        $catalogOptions = $product->getOptions();
        $result = [];
        foreach ($catalogOptions as $option) {
            $result[] = $this->dcoProcessor->getByOptionId($option->getOptionId());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getByOptionId(int $optionId)
    {
        return $this->dcoProcessor->getByOptionId($optionId);
    }

    /**
     * @inheritDoc
     */
    public function saveById(
        int $optionId,
        DependentOptionInterface $dependentOption
    ) {
        if ($dependentOption->getOptionId()) {
            if ($optionId != $dependentOption->getOptionId()) {
                throw new InputException(__('Invalid Option ID'));
            }
            $dependentOption->setOptionId($optionId);
        }
        return $this->save($dependentOption);
    }

    /**
     * @inheritDoc
     */
    public function save(
        DependentOptionInterface $dependentOption
    ) {
        $optionId = $this->dcoProcessor->saveOption($dependentOption);
        return $this->getByOptionId($optionId);
    }
}
