<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Data\Collection\AbstractDb as Collection;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as Resource;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Skeleton for MassActions.
 */
abstract class AbstractMassActions extends Action
{
    public const ACTIVATE = 'activate';
    public const INACTIVATE = 'inactivate';
    public const DELETE = 'delete';

    public const ALLOWED_ACTIONS = [self::ACTIVATE, self::INACTIVATE, self::DELETE];

    /**
     * @var Filter;
     */
    protected $filter;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        Collection $collection,
        RedirectFactory $redirectFactory,
        Resource $resource
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collection = $collection;
        $this->redirectFactory = $redirectFactory;
        $this->resource = $resource;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->filter->getCollection($this->collection);
        $action = $this->getRequest()->getParam('action');

        if (in_array($action, self::ALLOWED_ACTIONS)) {
            switch ($action) {
                case self::DELETE:
                    $this->massDelete();
                    break;
                case self::INACTIVATE:
                    $this->massStatusUpdate(0);
                    break;
                case self::ACTIVATE:
                    $this->massStatusUpdate(1);
                    break;
            }
        }

        return $this->redirectFactory->create()->setPath('*/*/');
    }

    protected function massDelete()
    {
        $size = $this->collection->getSize();

        try {
            $this->collection->walk(self::DELETE);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t delete records(s) right now. Please review log and try again.')
            );

            return;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $size));
    }

    /**
     * @param int $status
     */
    protected function massStatusUpdate($status)
    {
        $recordsUpdated = 0;

        try {
            /** @var \Magento\Framework\Model\AbstractModel $record */
            foreach ($this->collection->getItems() as $record) {
                $record->setIsActive($status);
                $this->resource->save($record);
                $recordsUpdated++;
            }
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t update some items. Please review log and try again.')
            );

            return;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $recordsUpdated));
    }
}
