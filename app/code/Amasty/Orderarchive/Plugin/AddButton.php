<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Plugin;

use Amasty\Orderarchive\Helper\Data;

class AddButton
{
    public const ADD_TO_ARCHIVE_COMPONENT_NAMESPACE = 'add_to_archive';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Amasty\Orderarchive\Model\ResourceModel\OrderGrid
     */
    protected $orderGrid;

    /**
     * AddButton constructor.
     * @param Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Amasty\Orderarchive\Model\ResourceModel\OrderGrid $orderGrid
     */
    public function __construct(
        Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Amasty\Orderarchive\Model\ResourceModel\OrderGrid $orderGrid
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @return null
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $subject)
    {
        if ($this->helper->isModuleOn()) {
            $buttonParams = $this->getButtonData($subject);

            $subject->addButton(
                $buttonParams['buttonId'],
                [
                    'label'     => $buttonParams['buttonName'],
                    'onclick'   => "confirmSetLocation('{$buttonParams['message']}', '{$buttonParams['buttonUrl']}')",
                ]
            );
        }
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $block
     * @return array
     */
    protected function getButtonData($block)
    {
        if ($this->orderGrid->isArchived($block->getOrderId())) {
            return [
                'message'    => __('Are you sure you want to remove from archive this order?'),
                'buttonId'   => 'remove_from_archive_order',
                'buttonUrl'  => $block->getUrl('amastyorderarchive/archive/OrderFromArchive'),
                'buttonName' => __('Remove From Archive'),
            ];
        }

        return [
            'message'    => __('Are you sure you want to archive this order?'),
            'buttonId'   => 'archive_order',
            'buttonUrl'  => $block->getUrl('amastyorderarchive/archive/OrderToArchive'),
            'buttonName' => __('Archiving'),
        ];
    }
}
