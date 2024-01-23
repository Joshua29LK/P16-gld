<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Plugin;

use Amasty\Orderarchive\Controller\Adminhtml\Customer\Archive;
use Amasty\Orderarchive\Helper\Data;
use Amasty\Orderarchive\Model\ResourceModel\OrderGrid;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

class AddButton
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var OrderGrid
     */
    private $orderGrid;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        Data $helper,
        OrderGrid $orderGrid,
        AuthorizationInterface $authorization
    ) {
        $this->helper = $helper;
        $this->orderGrid = $orderGrid;
        $this->authorization = $authorization;
    }

    public function beforeSetLayout(View $subject): void
    {
        if ($this->helper->isModuleOn() && $this->authorization->isAllowed(Archive::ADMIN_RESOURCE)) {
            $buttonParams = $this->getButtonData($subject);

            $subject->addButton(
                $buttonParams['buttonId'],
                [
                    'label'     => $buttonParams['buttonName'],
                    'onclick'   => "confirmSetLocation('{$buttonParams['message']}', '{$buttonParams['buttonUrl']}')",
                ]
            );

            if ($this->orderGrid->isArchived($subject->getOrderId())) {
                $subject->updateButton(
                    'back',
                    'onclick',
                    'setLocation(\'' . $subject->getUrl('amastyorderarchive/order/index') . '\')'
                );
            }
        }
    }

    private function getButtonData(View $block): array
    {
        if ($this->orderGrid->isArchived($block->getOrderId())) {
            $buttonData = [
                'message'    => __('Are you sure you want to remove from archive this order?'),
                'buttonId'   => 'remove_from_archive_order',
                'buttonUrl'  => $block->getUrl('amastyorderarchive/archive/OrderFromArchive'),
                'buttonName' => __('Remove From Archive'),
            ];
        } else {
            $buttonData = [
                'message'    => __('Are you sure you want to archive this order?'),
                'buttonId'   => 'archive_order',
                'buttonUrl'  => $block->getUrl('amastyorderarchive/archive/OrderToArchive'),
                'buttonName' => __('Archiving'),
            ];
        }

        return $buttonData;
    }
}
