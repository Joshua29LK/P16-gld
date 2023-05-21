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

namespace Mageprince\Productattach\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageprince\Productattach\Model\ResourceModel\Product as AttachmentResourceModel;
use Psr\Log\LoggerInterface;

class ProductAttachmentUpdateObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var AttachmentResourceModel
     */
    protected $attachResourceModel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProductAttachmentUpdateObserver constructor.
     * @param RequestInterface $request
     * @param AttachmentResourceModel $attachResourceModel
     */
    public function __construct(
        RequestInterface $request,
        AttachmentResourceModel $attachResourceModel,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->attachResourceModel = $attachResourceModel;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $productId = $observer->getEvent()->getProduct()->getId();
            $productParams = $this->request->getParams();

            $attachmentIds = [];
            if (!empty($productParams['links']['productattach'])) {
                $productAttachments = $productParams['links']['productattach'];
                foreach ($productAttachments as $attachment) {
                    if (isset($attachment['id'])) {
                        array_push($attachmentIds, $attachment['id']);
                    }
                }
            }
            $this->attachResourceModel->saveProductsRelationByProduct($attachmentIds, $productId);
        } catch (\Exception $e) {
            $this->logger->info('Something went wrong while saving product attachments');
        }

        return $this;
    }
}
