<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\ResourceModel;

class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Template\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Template constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Template\CollectionFactory $collectionFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Amasty\OrderStatus\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        $connectionName = null
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('amasty_orderstatus_status_template', 'id');
    }

    public function removeStatusTemplates($statusId)
    {
        /** @var Template\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status_id', ['eq' => $statusId]);
        /** @var \Amasty\OrderStatus\Model\Template $item */
        foreach ($collection as $item) {
            //phpcs:ignore
            $item->getResource()->delete($item);
        }
    }
}
