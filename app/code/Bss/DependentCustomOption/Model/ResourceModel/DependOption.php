<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DependOption extends AbstractDb
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init('bss_depend_co', 'dependent_id');
    }

    /**
     * @param AbstractModel $object
     * @param string|int $value
     * @param null $field
     * @return int|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function getDependId(AbstractModel $object, $value, $field = null)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'increment_id = ?',
            $value
        )->where(
            'product_id = ?',
            $object->getProductId()
        );

        $result = $this->getConnection()->fetchCol($select);

        return count($result) ? $result[0] : false;
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $incrementId = $this->getDependId($object, $value, $field);
        return parent::load($object, $incrementId);
    }

    /**
     * @param int $productId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastIncrementId($productId)
    {
        $bind = ['product_id' => $productId];
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['MAX(increment_id)']
        )->where(
            'product_id = :product_id'
        );

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @param int $optionId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionId($optionId)
    {
        $bind = ['option_id' => $optionId];
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'option_id = :option_id'
        )->order(
            'dependent_id DESC'
        )->limit(1);

        return $this->getConnection()->fetchRow($select, $bind);
    }

    /**
     * @param int $optionTypeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionTyeId($optionTypeId)
    {
        $bind = ['option_type_id' => $optionTypeId];
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'option_type_id = :option_type_id'
        )->order(
            'dependent_id DESC'
        )->limit(1);

        return $this->getConnection()->fetchRow($select, $bind);
    }

    /**
     * @param int $productId
     * @param int|array $optionId
     * @param int $type 1 is option_id, 2 is option_type_id
     * @throws LocalizedException
     * @return array
     */
    public function loadDcoByOption($productId, $optionId, $type = 1)
    {
        if ($productId) {
            $bindCol = null;
            if ($type === 1) {
                $bindCol = 'option_id';
            } elseif ($type === 2) {
                $bindCol = 'option_type_id';
            } elseif ($type === 3) {
                $bindCol = 'increment_id';
            }
            if (!$bindCol) {
                throw new LocalizedException(__('Bind data is invalid.'));
            }
            $select = $this->getConnection()->select()->from(
                $this->getMainTable()
            );
            if (is_array($optionId)) {
                $select->where(
                    $bindCol . ' IN(?)',
                    $optionId
                );
            } else {
                $select->where(
                    $bindCol . ' = ?',
                    $optionId
                );
            }
            $select->where(
                'product_id = ?',
                $productId
            )->order(
                'dependent_id DESC'
            );
            if (is_array($optionId)) {
                return $this->getConnection()->fetchAll($select);
            }
            $select->limit(1);
            return $this->getConnection()->fetchRow($select);
        }
        return null;
    }

    /**
     * @param array $dcoItem
     * @return $this
     * @throws LocalizedException
     */
    public function saveNewDco($dcoItem) {
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $dcoItem);

        return $this;
    }

    /**
     * @param array $dcoItems
     * @return $this
     * @throws LocalizedException
     */
    public function saveNewDcos($dcoItems) {
        $this->getConnection()->insertMultiple($this->getMainTable(), $dcoItems);

        return $this;
    }

    /**
     * @param int|string $productId
     * @return array
     */
    public function getAllOptionIdsByProduct($productId)
    {
        $options = $this->getOptionsByProduct($productId);
        $optionValues = $this->getOptionValuesByProduct($productId);
        $optionHasValues = [
            'checkbox',
            'drop_down',
            'radio',
            'multiple'
        ];
        $result = [];
        foreach ($options as $option) {
            if (!in_array($option['type'], $optionHasValues)) {
                $result[] = [
                    'option_id' => $option['option_id'],
                    'option_type_id' => null
                ];
            } else {
                foreach ($optionValues as $optionValue) {
                    if ($option['option_id'] == $optionValue['option_id']) {
                        $result[] = [
                            'option_id' => $option['option_id'],
                            'option_type_id' => $optionValue['option_type_id']
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param int|string $productId
     * @return array
     */
    protected function getOptionsByProduct($productId)
    {
        $select = $this->getConnection()->select();
        $optionTable = $this->_resources->getTableName('catalog_product_option');
        $select->from(
            ['optionTable' => $optionTable],
            ['option_id', 'type']
        )->where('optionTable.product_id = ?', $productId);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param int|string $productId
     * @return array
     */
    protected function getOptionValuesByProduct($productId)
    {
        $select = $this->getConnection()->select();
        $mainTable = $this->_resources->getTableName('catalog_product_option');
        $subTable = $this->_resources->getTableName('catalog_product_option_type_value');

        $select->from(
            ['valueTable' => $subTable],
            [
                'option_type_id',
                'option_id'
            ]
        )->joinInner(
            ['optionTable' => $mainTable],
            'valueTable.option_id=optionTable.option_id',
            ''
        )->where('optionTable.product_id = ?', $productId);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param int $productId
     * @param int $optionTypeId
     * @param string|int $dependOnValue
     * @throws LocalizedException
     */
    public function updateDependValue($productId, $optionTypeId, $dependOnValue)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['depend_value' => $dependOnValue],
            [
                'option_type_id=?' => $optionTypeId,
                'product_id=?' => $productId,
            ]
        );
    }

    /**
     * @param int $productId
     * @throws LocalizedException
     */
    public function removeOldDco($productId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['product_id=?' => $productId]
        );
    }

    public function getOptionTitlesbyProduct($product)
    {
        $optionTitleTable =  $this->_resources->getTableName('catalog_product_option_title');
        $optionTable =  $this->_resources->getTableName('catalog_product_option');
        $select = $this->getConnection()->select()->from(
            ['main_table' => $optionTable],
            ['option_id', 'type']
        )->join(
            ['option_title' => $optionTitleTable],
            'option_title.option_id = main_table.option_id',
            ['title' => 'title', 'store_id' => 'store_id']
        )->where(
            'main_table.product_id = ?',
            $product
        );

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getOptionValuesType()
    {
        $optionTitleTable = $this->getTable('catalog_product_option_type_title');
        $titleExpr = $this->getConnection()->getCheckSql(
            'store_value_title.title IS NULL',
            'default_value_title.title',
            'store_value_title.title'
        );

        $joinExpr = 'store_value_title.option_type_id = main_table.option_type_id';
        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->_resources->getTableName('catalog_product_option_type_value')],
            ['option_id', 'option_type_id']
        )->join(
            ['default_value_title' => $optionTitleTable],
            'default_value_title.option_type_id = main_table.option_type_id',
            ['default_title' => 'title']
        )->joinLeft(
            ['store_value_title' => $optionTitleTable],
            $joinExpr,
            ['store_title' => 'title', 'title' => $titleExpr]
        )->where(
            'default_value_title.store_id = ?',
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param $optionTypeId
     * @param int $type
     * @param null $storeId
     * @return bool
     */
    public function getTitle($optionTypeId, $type = 1, $storeId = null)
    {
        // $type = 1 get option title
        // $type = 2 get option value title
        $optionTitleTable = $this->getTable('catalog_product_option_title');
        if ($type == 2) {
            $optionTitleTable = $this->getTable('catalog_product_option_type_title');
        }
        $bindCol = $type == 1 ? 'option_id' : 'option_type_id';
        $select = $this->getConnection()->select()->from(
            ['main_table' => $optionTitleTable],
            ['title']
        )->where(
            'main_table.' . $bindCol . ' = ?',
            $optionTypeId
        );
        if ($storeId) {
            $select->where(
                'main_table.store_id = ?',
                $storeId
            );
        } else {
            $select->where(
                'main_table.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        }
        $select->limit(1);
        $data = $this->getConnection()->fetchRow($select);
        return $data && isset($data['title']) ? $data['title'] : false;
    }

    /**
     * @param int|string $productId
     * @return array
     * @throws LocalizedException
     */
    public function getDcoByProduct($productId)
    {
        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            ['option_type_id', 'option_id', 'product_id', 'depend_value']
        )->where(
            'main_table.product_id = ?',
            $productId
        );
        return $this->getConnection()->fetchAll($select);
    }
}
