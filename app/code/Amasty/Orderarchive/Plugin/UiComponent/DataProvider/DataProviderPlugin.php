<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Plugin\UiComponent\DataProvider;

use Magento\Framework\App\RequestInterface;

class DataProviderPlugin
{
    protected $sourceMap = [
        'sales_order_view_invoice_grid_data_source' => 'amasty_sales_invoice_archives_grid_data_source',
        'sales_order_view_shipment_grid_data_source' => 'amasty_sales_shipment_archives_grid_data_source',
        'sales_order_view_creditmemo_grid_data_source' => 'amasty_sales_creditmemo_archives_grid_data_source'
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\Orderarchive\Model\ResourceModel\OrderGrid
     */
    private $orderArchive;

    public function __construct(
        RequestInterface $request,
        \Amasty\Orderarchive\Model\ResourceModel\OrderGrid $orderArchive
    ) {
        $this->request = $request;
        $this->orderArchive = $orderArchive;
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject
     * @param \Magento\Framework\Api\Search\SearchCriteria                          $result
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function afterGetSearchCriteria(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject,
        $result
    ) {
        $requestName = $result->getRequestName();
        if (isset($this->sourceMap[$requestName])
            && $this->request->getParam('order_id')
            && $this->orderArchive->isArchived($this->request->getParam('order_id'))
        ) {
            $result->setRequestName($this->sourceMap[$requestName]);
        }

        return $result;
    }
}
