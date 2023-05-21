<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Source;

use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class State implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CollectionFactory
     */
    private $statusCollectionFactory;

    public function __construct(
        CollectionFactory $statusCollectionFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var \Magento\Sales\Model\Order\Status[] $statusItems */
            $statusItems = $this->statusCollectionFactory->create()->getItems();
            foreach ($statusItems as $status) {
                $this->options[] = ['value' => $status->getStatus(), 'label' => $status->getLabel()];
            }
        }

        return $this->options;
    }
}
