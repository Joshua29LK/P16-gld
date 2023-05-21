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

namespace Mageprince\Productattach\Plugin;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\LocalizedException;
use Mageprince\Productattach\Model\ResourceModel\Product as ProductAttachProductResourceModel;
use Psr\Log\LoggerInterface;

class ProductAttachment
{
    /**
     * @var ProductExtensionFactory
     */
    protected $productExtensionFactory;

    /**
     * @var ProductAttachProductResourceModel
     */
    protected $productRelationModel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProductAttachment constructor.
     * @param ProductExtensionFactory $productExtensionFactory
     * @param ProductAttachProductResourceModel $productRelationModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductExtensionFactory $productExtensionFactory,
        ProductAttachProductResourceModel $productRelationModel,
        LoggerInterface $logger
    ) {
        $this->productExtensionFactory = $productExtensionFactory;
        $this->productRelationModel = $productRelationModel;
        $this->logger = $logger;
    }

    /**
     * Get attachments after get product
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws LocalizedException
     */
    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $product
    ) {
        if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getAttachments()) {
            return $product;
        }

        if (!$product->getExtensionAttributes()) {
            $productExtension = $this->productExtensionFactory->create();
            $product->setExtensionAttributes($productExtension);
        }

        try {
            $attachmentIds = $this->productRelationModel->getAttachmentsByProductId($product->getId());
            if ($attachmentIds) {
                $product->getExtensionAttributes()->setAttachments($attachmentIds);
            }
        } catch (\Exception $e) {
            $message = __('Attachment get error: %1', $e->getMessage());
            $this->logger->info($message);
        }

        return $product;
    }

    /**
     * Get attachments after get product list
     *
     * @param ProductRepositoryInterface $subject
     * @param SearchResults $searchResult
     * @return SearchResults
     */
    public function afterGetList(
        ProductRepositoryInterface $subject,
        SearchResults $searchResult
    ) {
        try {
            foreach ($searchResult->getItems() as $product) {
                $extensionAttributes = $product->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->productExtensionFactory->create();
                }
                $attachmentIds = $this->productRelationModel->getAttachmentsByProductId($product->getId());
                $extensionAttributes->setAttachments($attachmentIds);
                $product->setExtensionAttributes($extensionAttributes);
            }
        } catch (\Exception $e) {
            $message = __('Attachment get error: %1', $e->getMessage());
            $this->logger->info($message);
        }

        return $searchResult;
    }

    /**
     * Save attachments after product save
     *
     * @param ProductRepositoryInterface $subject
     * @param \Closure $proceed
     * @param ProductInterface $product
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundSave(
        ProductRepositoryInterface $subject,
        \Closure $proceed,
        ProductInterface $product
    ) {
        try {
            $extensionAttributes = $product->getExtensionAttributes();
            $attachmentIds = $extensionAttributes->getAttachments();
            $result = $proceed($product);
            $productId = $result->getId();

            if (is_array($attachmentIds)) {
                $this->productRelationModel->saveProductsRelationByProduct($attachmentIds, $productId);
            }

            $attachmentIds = $this->productRelationModel->getAttachmentsByProductId($productId);
            $result->getExtensionAttributes()->setAttachments($attachmentIds);
            return $result;
        } catch (\Exception $e) {
            $message = __('Attachment save error: %1', $e->getMessage());
            $this->logger->info($message);
            return $proceed($product);
        }
    }
}
