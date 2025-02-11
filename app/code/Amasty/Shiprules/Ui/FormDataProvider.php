<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Ui;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Shiprules\Model\ResourceModel\Rule\CollectionFactory;
use Amasty\Shiprules\Model\Rule;

/**
 * Data Provider for amasty_shiprules_form.
 */
class FormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->collection->joinRelationTables();
        $items = $this->collection->getItems();
        /** @var Rule $rule */
        foreach ($items as $rule) {
            $this->loadedData[$rule->getId()] = $rule->prepareForEdit()->toArray();
            $this->loadedData[$rule->getId()][Rule::EXTENSION_ATTRIBUTES_KEY]
                = $rule->getExtensionAttributes()->__toArray();
        }

        $data = $this->dataPersistor->get(\Amasty\Shiprules\Model\ConstantsInterface::DATA_PERSISTOR_FORM);

        if (!empty($data)) {
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->dataPersistor->clear(\Amasty\Shiprules\Model\ConstantsInterface::DATA_PERSISTOR_FORM);
        }

        return $this->loadedData;
    }

    // During filtering by 'rule_id',
    // we need to indicate to which table we want to add filter,
    // to avoid exception "Column 'rule_id' in where clause is ambiguous".
    // Because column 'rule_id' exist in all relation tables.
    public function addFilter(Filter $filter): void
    {
        if ($filter->getField() === RuleInterface::RULE_ID) {
            $this->getCollection()->addFieldToFilter(
                'main_table.' . RuleInterface::RULE_ID,
                [$filter->getConditionType() => $filter->getValue()]
            );
        } else {
            parent::addFilter($filter);
        }
    }
}
