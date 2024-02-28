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
 * @copyright  Copyright (c) 2020-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
declare(strict_types=1);

namespace Bss\DependentCustomOption\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

class Dco extends DependOption
{
    /**
     * Migrate data.
     *
     * @param int $limitProduct
     * @return array
     * @throws \Exception
     */
    public function migrate($limitProduct = 0)
    {
        $rows['success'] = 0;
        $rows['error'] = "";

        // Get list all dco in db
        $oldDcoList = $this->getAllDco();

        // Get product by OptionID
        $optionIds = [];
        $optionTypeIds = [];
        foreach ($oldDcoList as $oldDcoItem) {
            if (!in_array($oldDcoItem['option_id'], $optionIds)) {
                $optionIds[] = $oldDcoItem['option_id'];
            }

            if (!in_array($oldDcoItem['option_type_id'], $optionTypeIds)) {
                $optionTypeIds[] = $oldDcoItem['option_type_id'];
            }

            // Check data old (data version < 1.0.4), if not then skip migrate data.
            if (isset($oldDcoItem['increment_id'])) {
                return $rows;
            }
        }

        $optionTypeList = $this->getOptionTypeList($optionTypeIds);
        foreach ($optionTypeList as $optionTypeItem) {
            if (isset($optionTypeItem['option_id']) && !in_array($optionTypeItem['option_id'], $optionIds)) {
                $optionIds[] = $optionTypeItem['option_id'];
            }
        }
        $optionListItems = $this->getOptionListItems($optionIds);

        // Process data
        $oldDcoData = [];
        foreach ($optionListItems as $optionItem) {
            if (!in_array($optionItem['product_id'], $oldDcoData)) {
                $oldDcoData[] = $optionItem['product_id'];
            }
        }

        $processAllThis = $oldDcoData;
        if ($limitProduct > 0) {
            $processAllThis = array_slice($oldDcoData, 0, $limitProduct);
        }

        foreach ($processAllThis as $productId) {
            try {
                $rows['success'] += (int) $this->saveNewDcoData($productId);
            } catch (\Exception $e) {
                $rows['error'] .= $productId . ', ';
            }
        }
        return $rows;
    }

    /**
     * Get list all dco in db.
     *
     * @return array
     */
    public function getAllDco()
    {
        $select = $this->getConnection()->select();
        $optionTable = $this->_resources->getTableName('bss_depend_co');
        $select->from(
            ['optionTable' => $optionTable],
            ['increment_id', 'depend_value', 'option_type_id', 'option_id', 'product_id']
        );

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Get option type list
     *
     * @param array $optionTypeIds
     * @return array
     */
    public function getOptionTypeList($optionTypeIds)
    {
        $select = $this->getConnection()->select();
        $optionTypeTable = $this->_resources->getTableName('catalog_product_option_type_value');
        $select->from(
            ['optionTypeTable' => $optionTypeTable],
            ['option_type_id', 'option_id']
        )->where(
            'optionTypeTable.option_type_id IN (?)',
            $optionTypeIds
        );
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Get option type list items
     *
     * @param array $optionIds
     * @return array
     */
    public function getOptionListItems($optionIds)
    {
        $select = $this->getConnection()->select();
        $optionTypeTable = $this->_resources->getTableName('catalog_product_option');
        $select->from(
            ['optionTable' => $optionTypeTable],
            ['option_id', 'product_id']
        )->where(
            'optionTable.option_id IN (?)',
            $optionIds
        );
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Save new dco
     *
     * @param int $productId
     * @return int|null
     * @throws LocalizedException
     */
    public function saveNewDcoData($productId)
    {
        // Get all options
        $allOptions = $this->getAllOptionIdsByProduct($productId);

        $arrOptionType = [];
        $boilerplateList = [];
        $lastIncrementProduct = 1;
        // Get options dco data to insert
        foreach ($allOptions as $option) {
            if (isset($option['option_id']) && $option['option_id'] && !isset($boilerplateList[$option['option_id']])) {
                $boilerplateOption = $this->createBoilerplateDco(
                    $lastIncrementProduct,
                    $productId,
                    $option['option_id'],
                    null,
                    1
                );

                // Get option values dco data to insert
                foreach ($allOptions as $optionAnother) {
                    if (!isset($boilerplateList[$option['option_id']][$optionAnother['option_type_id']]) &&
                        isset($optionAnother['option_type_id']) && $optionAnother['option_type_id'] &&
                        isset($optionAnother['option_id']) && $optionAnother['option_id'] &&
                        $optionAnother['option_id'] == $option['option_id']
                    ) {
                        $boilerplateOptionType = $this->createBoilerplateDco(
                            $lastIncrementProduct,
                            $productId,
                            $optionAnother['option_type_id'],
                            null,
                            2
                        );
                        $boilerplateList[$option['option_id']][] = $boilerplateOptionType;
                    }
                }

                // Re-sort list
                $boilerplateList[$option['option_id']][] = $boilerplateOption;
            }

            // Logic change depend_value: depend_id to increment_id
            $arrOptionType['option_id'][] = $option['option_id'];
            $arrOptionType['option_type_id'][] = $option['option_type_id'];
        }

        // Get all list data will be inserted
        $list = [];
        foreach ($boilerplateList as $boilerplate) {
            foreach ($boilerplate as $item) {
                $list[] = $item;
            }
        }

        if (!$list) {
            return 0;
        }

        // Logic change depend_value: depend_id to increment_id
        $allOldData = $arrOptionType ? $this->getOldDcoByOptions($arrOptionType) : [];
        $listOldDco = [];
        foreach ($allOldData as $data) {
            $listOldDco[$data['dependent_id']] = $data;
        }

        // Save new depend_value
        foreach ($listOldDco as $dco) {
            if (isset($dco['depend_value'])) {
                $oldDependVal = explode(",", $dco['depend_value']);
                $newDependVal = [];
                $type = '';

                foreach ($oldDependVal as $val) {
                    if (!empty($listOldDco[$val]['option_id'])) {
                        $value = $listOldDco[$val]['option_id'];
                        $type = 'option_id';
                    } elseif (!empty($listOldDco[$val]['option_type_id'])) {
                        $value = $listOldDco[$val]['option_type_id'];
                        $type = 'option_type_id';
                    }

                    if (!empty($value)) {
                        $newDependVal[] = $this->convertToIncrementId($list, $type, $value);
                    }
                }

                // Change depend_value in last data.
                if ($newDependVal) {
                    $list = $this->changeDependValue($list, $newDependVal, $dco['option_type_id']);
                }
            }
        }

        // Remove old dco of product
        $this->removeDcoByOption($allOptions);

        // Insert new data
        $this->saveNewDcos($list);
        return count($list);
    }

    /**
     * Convert depend value old: depend_id to increment_id
     *
     * @param array $list
     * @param string $type
     * @param string $value
     * @return int
     */
    public function convertToIncrementId($list, $type, $value)
    {
        foreach ($list as $data) {
            if ($data[$type] == $value) {
                return $data['increment_id'];
            }
        }

        return 0;
    }

    /**
     * Change depend value in last data.
     *
     * @param array $list
     * @param array $newDependVal
     * @param string $optionVal
     * @return array
     */
    public function changeDependValue($list, $newDependVal, $optionVal)
    {
        foreach ($list as $key => $data) {
            if (!empty($data['option_type_id']) && $data['option_type_id'] == $optionVal) {
                $list[$key]['depend_value'] = implode(",", array_unique($newDependVal));
                return $list;
            }
        }

        return $list;
    }

    /**
     * Create boiler plate dco.
     *
     * @param int $incrementId
     * @param int $productId
     * @param int $optionId
     * @param null|mixed $dependValue
     * @param int $type = 1 is option_id 2 is option_type_id
     * @return array
     */
    public function createBoilerplateDco(
        &$incrementId,
        $productId,
        $optionId,
        $dependValue = null,
        $type = 1
    ) {
        $boilerplate = [
            'increment_id' => $incrementId,
            'product_id' => $productId,
            'depend_value' => $dependValue
        ];
        if ($type === 1) {
            $boilerplate['option_id'] = $optionId;
            $boilerplate['option_type_id'] = null;
        } elseif ($type === 2) {
            $boilerplate['option_id'] = null;
            $boilerplate['option_type_id'] = $optionId;
        }

        $incrementId++;
        return $boilerplate;
    }

    /**
     * Save new dco
     *
     * @param array $dcoItems
     * @return $this
     * @throws LocalizedException
     */
    public function saveNewDcos($dcoItems)
    {
        $this->getConnection()->insertMultiple($this->getMainTable(), $dcoItems);
        return $this;
    }

    /**
     * Remove old dco
     *
     * @param array $options
     * @throws LocalizedException
     */
    public function removeDcoByOption($options = [])
    {
        $optionIds = [];
        $optionTypeIds = [];
        foreach ($options as $option) {
            if (isset($option['option_id']) && $option['option_id']) {
                $optionIds[] = $option['option_id'];
            }
            if (isset($option['option_type_id']) && $option['option_type_id']) {
                $optionTypeIds[] = $option['option_type_id'];
            }
        }
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                'option_id IN (?)' => $optionIds
            ]
        );
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                'option_type_id IN (?)' => $optionTypeIds
            ]
        );
    }

    /**
     * Get old Dco by options.
     *
     * @param array $allOption
     * @return array
     * @throws LocalizedException
     */
    public function getOldDcoByOptions($allOption)
    {
        $oldOptionId = isset($allOption['option_id']) ? array_unique($allOption['option_id']) : 0;
        $oldOptionTypeId = isset($allOption['option_type_id']) ? array_unique($allOption['option_type_id']) : 0;

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            ['dependent_id', 'option_type_id', 'option_id', 'product_id', 'depend_value', 'increment_id']
        )->where(
            'main_table.option_id IN (?)',
            $oldOptionId
        )->orWhere(
            'main_table.option_type_id IN (?)',
            $oldOptionTypeId
        );

        return $this->getConnection()->fetchAll($select);
    }
}
