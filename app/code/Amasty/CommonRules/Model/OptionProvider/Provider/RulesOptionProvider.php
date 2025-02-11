<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\OptionProvider\Provider;

/**
 * Provider
 */
class RulesOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const PAGE_SIZE = 7500;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $rules = [
            [
                'value' => '0',
                'label' => ' '
            ]
        ];
        $pageNum = 1;

        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->setPageSize(self::PAGE_SIZE)
            ->setCurPage($pageNum);

        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['rule_id', 'name']);

        while ($pageNum <= $collection->getLastPageNumber()) {
            foreach ($collection->getData() as $rule) {
                $rules[] = [
                    'value' => $rule['rule_id'],
                    'label' => $rule['name']
                ];
            }
            $collection->setCurPage(++$pageNum)->resetData();
        }

        return $rules;
    }
}
