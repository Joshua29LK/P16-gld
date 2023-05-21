<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Amasty\Orderarchive\Model\ArchiveFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

abstract class Archive extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Orderarchive::orderarchive_grid';

    public const ADD_TO_ARCHIVE_METHOD_CODE = 'addToArchive';

    public const REMOVE_FROM_ARCHIVE_METHOD_CODE = 'removeFromArchive';

    public const REMOVE_PERMANENTLY_METHOD_CODE = 'removePermanently';

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * @var ArchiveFactory
     */
    protected $archiveFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $allowedMethods = [
        self::ADD_TO_ARCHIVE_METHOD_CODE,
        self::REMOVE_FROM_ARCHIVE_METHOD_CODE,
        self::REMOVE_PERMANENTLY_METHOD_CODE
    ];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Amasty\Orderarchive\Model\OrderProcessor
     */
    protected $orderProcessor;

    /**
     * Archive constructor.
     *
     * @param Action\Context                                     $context
     * @param \Amasty\Orderarchive\Helper\Data                   $helper
     * @param \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor
     * @param CollectionFactory                                  $collectionFactory
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor,
        CollectionFactory $collectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Orders Archives'), __('Orders Archives'));

        return $resultPage;
    }

    /**
     * @param array $selectedOrders
     * @param string $method
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     * @deprecated 1.1.0 precess method moved to OrderProcessor class
     */
    protected function process(array $selectedOrders, $method = self::ADD_TO_ARCHIVE_METHOD_CODE)
    {
        return $this->orderProcessor->process($selectedOrders, $method);
    }
}
