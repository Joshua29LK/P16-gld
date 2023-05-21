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

namespace Mageprince\Productattach\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Mageprince\Productattach\Api\Data\AttachmentSearchResultsInterfaceFactory;
use Mageprince\Productattach\Api\Data\ProductattachTableInterface;
use Mageprince\Productattach\Api\Data\ProductattachTableInterfaceFactory;
use Mageprince\Productattach\Api\ProductattachInterface;
use Mageprince\Productattach\Helper\Data as HelperData;
use Mageprince\Productattach\Model\ResourceModel\Product as ProductRelationModel;
use Mageprince\Productattach\Model\ResourceModel\Productattach\Collection as AttachmentCollection;
use Mageprince\Productattach\Model\ResourceModel\Productattach\CollectionFactory as AttachmentCollectionFactory;

class ProductattachWebApi implements ProductattachInterface
{
    /**
     * Attachment not found message
     */
    const ATTACHMENT_NOT_FOUND_MESSAGE = 'Attachment Not Found';

    /**
     * @var ProductattachFactory
     */
    protected $productattachFactory;

    /**
     * @var ProductattachTableInterfaceFactory
     */
    protected $productattachTableInterfaceFactory;

    /**
     * @var HelperData
     */
    protected $dataHelper;

    /**
     * @var ProductRelationModel
     */
    protected $productRelationModel;

    /**
     * @var ResourceModel\Productattach\Collection
     */
    protected $attachmentCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var AttachmentSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * ProductattachWebApi constructor.
     *
     * @param ProductattachFactory $productattachFactory
     * @param ProductattachTableInterfaceFactory $productattachTableInterfaceFactory
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param AttachmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param HelperData $dataHelper
     * @param ProductRelationModel $productRelationModel
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RequestInterface $request
     */
    public function __construct(
        ProductattachFactory $productattachFactory,
        ProductattachTableInterfaceFactory $productattachTableInterfaceFactory,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        AttachmentSearchResultsInterfaceFactory $searchResultsFactory,
        HelperData $dataHelper,
        ProductRelationModel $productRelationModel,
        CollectionProcessorInterface $collectionProcessor,
        RequestInterface $request
    ) {
        $this->productattachFactory = $productattachFactory;
        $this->productattachTableInterfaceFactory = $productattachTableInterfaceFactory;
        $this->dataHelper = $dataHelper;
        $this->productRelationModel = $productRelationModel;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->request = $request;
    }

    /**
     * Add Update attachment
     * @param int $attachmentId
     * @param string $name
     * @param string $description
     * @param string $url
     * @param string $products
     * @param string $store
     * @param string $customerGroup
     * @param string $active
     * @return ProductattachTableInterface
     * @throws NotFoundException
     */
    public function updateInsertAttachment(
        $attachmentId,
        $name,
        $description,
        $url,
        $products,
        $store,
        $customerGroup,
        $active
    ) {
        $productAttachModel = $this->productattachFactory->create();

        if ($attachmentId) {
            $productAttachModel->load($attachmentId);
        }

        $productAttachModel->addData([
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'store' => $store,
            'customer_group' => $customerGroup,
            'active' => $active
        ]);

        try {
            $file = $this->request->getFiles();
            if (!empty($file['file']['name'])) {
                $this->dataHelper->uploadFile('file', $productAttachModel);
            }
            $attachment = $productAttachModel->save();
            $productIds = explode(',', $products);
            $this->productRelationModel->saveProductsRelation($attachment, $productIds);
            $attachmentFactory = $this->insertAttachmentInterfaceData($attachment);
        } catch (\Exception $e) {
            throw new NotFoundException(
                __('Something went wrong while saving attachment', $e->getMessage())
            );
        }

        return $attachmentFactory;
    }

    /**
     * Delete attachment
     * @param int $attachmentId
     * @return bool
     * @throws \Exception
     */
    public function deleteAttachment($attachmentId)
    {
        $attachment = $this->productattachFactory->create();
        $attachment->load($attachmentId);
        if ($attachment->getId()) {
            $attachment->delete();
        } else {
            throw new NotFoundException(
                __(self::ATTACHMENT_NOT_FOUND_MESSAGE)
            );
        }
        return true;
    }

    /**
     * Get attachment by id
     * @param int $attachmentId
     * @return ProductattachTableInterface
     * @throws NotFoundException
     */
    public function getAttachment($attachmentId)
    {
        $attachment = $this->productattachFactory->create();
        $attachment->load($attachmentId);
        if ($attachment->getId()) {
            $attachmentFactory = $this->insertAttachmentInterfaceData($attachment);
        } else {
            throw new NotFoundException(
                __(self::ATTACHMENT_NOT_FOUND_MESSAGE)
            );
        }
        return $attachmentFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var AttachmentCollection $collection */
        $collection = $this->attachmentCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param $attachment
     * @return ProductattachTableInterface
     */
    public function insertAttachmentInterfaceData($attachment)
    {
        $attachmentFactory = $this->productattachTableInterfaceFactory->create();
        $attachmentFactory->setAttachmentId($attachment->getId());
        $attachmentFactory->setName($attachment->getName());
        $attachmentFactory->setDescription($attachment->getDescription());
        $attachmentFactory->setFile($attachment->getFile());
        $attachmentFactory->setFileType($attachment->getFileExt());
        $attachmentFactory->setUrl($attachment->getUrl());
        $attachmentFactory->setProducts($attachment->getProducts($attachment));
        $attachmentFactory->setCustomerGroup($attachment->getCustomerGroup());
        $attachmentFactory->setStore($attachment->getStore());
        $attachmentFactory->setActive($attachment->getActive());
        return $attachmentFactory;
    }

    /**
     * @inheridoc
     */
    public function getAttachmentByProductId($productId)
    {
        $attachmentIds = $this->productRelationModel->getAttachmentsByProductId($productId);
        $attachments = [];
        foreach ($attachmentIds as $attachmentId) {
            $attachments[] = $this->getAttachment($attachmentId);
        }
        return $attachments;
    }
}
