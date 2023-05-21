<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\FlagAssign;

use Amasty\Flags\Api\FlagRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\FlagAssign as FlagAssignAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassAssign extends FlagAssignAction
{
    /**
     * @var FlagRepositoryInterface
     */
    private $flagRepository;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Order\Flag
     */
    private $orderFlagResource;

    public function __construct(
        Context $context,
        Filter $filter,
        FlagRepositoryInterface $flagRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Amasty\Flags\Model\ResourceModel\Order\Flag $orderFlagResource
    ) {
        parent::__construct($context);
        $this->flagRepository = $flagRepository;
        $this->filter = $filter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderFlagResource = $orderFlagResource;
    }

    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'sales/order';
    }

    public function execute()
    {
        $orderCollection = $this->filter->getCollection($this->orderCollectionFactory->create());

        try {
            if ($orderCollection->getSize() <= 0) {
                throw new LocalizedException(__('No orders selected.'));
            }

            $columnId = (int)$this->getRequest()->getParam('column');
            $flagId = (int)$this->getRequest()->getParam('flag');

            if (!$columnId || !$flagId) {
                throw new LocalizedException(__('Bad request'));
            }

            $orderIds = $orderCollection->getColumnValues($orderCollection->getResource()->getIdFieldName());

            $this->orderFlagResource->massAssign($orderIds, $columnId, $flagId);

            $this->messageManager->addSuccessMessage(__('The flags have been successfully assigned.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
    }
}
