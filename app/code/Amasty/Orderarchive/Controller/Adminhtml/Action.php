<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Controller\Adminhtml;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class AbstractMassStatus
 */
abstract class Action extends \Amasty\Orderarchive\Controller\Adminhtml\Archive
{

    public const NAMESPACE_ARCHIVE = 'amasty_sales_order_archives_grid';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';

    /**
     * @var \Amasty\Orderarchive\Model\ResourceModel\OrderGrid\CollectionFactory
     */
    protected $orderArchiveCollectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor,
        CollectionFactory $collectionFactory,
        \Amasty\Orderarchive\Model\ResourceModel\OrderGrid\CollectionFactory $orderArchiveCollectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Filter $filter
    ) {
        parent::__construct($context, $helper, $orderProcessor, $collectionFactory, $resultPageFactory);
        $this->filter = $filter;
        $this->orderArchiveCollectionFactory = $orderArchiveCollectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest()->getParams();

        if (array_key_exists('namespace', $request) && ($request['namespace'] == self::NAMESPACE_ARCHIVE)) {
            $collectionFactory = $this->orderArchiveCollectionFactory->create();
        } else {
            $collectionFactory = $this->collectionFactory->create();
        }

        try {
            $collection = $this->filter->getCollection($collectionFactory);
            if ($collection->getSize()) {
                $this->massAction($collection->getAllIds());
            } else {
                $this->messageManager->addErrorMessage(__('Please Select Items'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }

    /**
     * @param array $selectedIds
     * @return void
     */
    abstract protected function massAction($selectedIds);
}
