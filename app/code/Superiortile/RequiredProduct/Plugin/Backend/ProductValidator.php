<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Plugin\Backend;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Superiortile\RequiredProduct\Helper\Data;

/**
 * Class Superiortile\RequiredProduct\Plugin\Backend\ProductValidator
 */
class ProductValidator
{
    /**
     * @var Data
     */

    private $helper;
    /**
     * @var CollectionFactory
     */
    private $attributeCollection;

    /**
     * @param CollectionFactory $attributeCollection
     * @param Data $helper
     */
    public function __construct(
        CollectionFactory $attributeCollection,
        Data $helper
    ) {
        $this->helper = $helper;
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * Validate Product Data
     *
     * @param \Magento\Catalog\Model\Product\Validator $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param RequestInterface $request
     * @param DataObject $response
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeValidate(
        \Magento\Catalog\Model\Product\Validator $subject,
        \Magento\Catalog\Model\Product $product,
        RequestInterface $request,
        DataObject $response
    ) {
        $linksData = $request->getParam('links');
        $productData = $request->getParam('product');
        if (!$linksData || !is_array($linksData)) {
            return [$product, $request, $response];
        }
        $errorCodes = [];
        $productLinkTypes = array_keys($this->helper->getRequiredProductLinkTypeIdByCode());
        foreach ($productLinkTypes as $linkType) {
            if (!isset($linksData[$linkType])) {
                continue;
            }
            $typeId = substr($linkType, -1);

            if (empty($productData[sprintf('require_name_%s', $typeId)]) ||
                !trim($productData[sprintf('require_name_%s', $typeId)])) {
                $errorCodes[] = sprintf('require_name_%s', $typeId);
            }
        }

        $error = [];

        if ($errorCodes) {
            $attributes = $this->attributeCollection->create()
                ->addFieldToFilter('attribute_code', ['in' => $errorCodes]);
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            foreach ($attributes as $attribute) {
                $error[] = sprintf('"%s"', $attribute->getFrontendLabel());
            }
        }

        if ($error) {
            throw new LocalizedException(__('Attribute(s) %1 is required.', implode(', ', $error)));
        }

        return [$product, $request, $response];
    }
}
