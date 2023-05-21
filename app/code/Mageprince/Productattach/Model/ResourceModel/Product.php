<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Model\ResourceModel;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('prince_productattach_relation', 'productattach_id');
    }

    /**
     * Save product relation
     * @param \Mageprince\Productattach\Model\Productattach $model
     * @param $productIds
     * @throws \Exception
     */
    public function saveProductsRelation(\Mageprince\Productattach\Model\Productattach $model, $productIds)
    {
        try {
            $oldProducts = (array) $model->getProducts($model);
            $newProducts = (array) $productIds;

            $connection = $this->getConnection();

            $table = $this->getTable($this->getMainTable());
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);

            if ($delete) {
                $where = [
                    $this->getIdFieldName().' = ?' => (int)$model->getId(),
                    'product_id IN (?)' => $delete
                ];
                $connection->delete($table, $where);
            }

            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    $data[] = [
                        $this->getIdFieldName() => (int)$model->getId(),
                        'product_id' => (int)$productId
                    ];
                }
                $connection->insertMultiple($table, $data);
            }
        } catch (\Exception $e) {
            throw new $e('Something went wrong while saving the product attachment.');
        }
    }

    /**
     * Save product relation by product id
     * @param $attachmentIds
     * @param $attachmentProductId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProductsRelationByProduct($attachmentIds, $attachmentProductId)
    {
        $oldProducts = $this->getAttachmentsByProductId($attachmentProductId);
        $newProducts = $attachmentIds;

        if (isset($newProducts)) {
            $table = $this->getTable($this->getMainTable());
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);

            try {
                if ($delete) {
                    $where = [
                        'product_id = ?' => (int)$attachmentProductId,
                        $this->getIdFieldName() . ' IN (?)' => $delete
                    ];
                    $this->getConnection()->delete($table, $where);
                }
                if ($insert) {
                    $data = [];
                    foreach ($insert as $productId) {
                        $data[] = [
                            $this->getIdFieldName() => (int)$productId,
                            'product_id' => (int)$attachmentProductId
                        ];
                    }
                    $this->getConnection()->insertMultiple($table, $data);
                }
            } catch (\Exception $e) {
                //TODO::Print Log
            }
        }

        return $this;
    }

    /**
     * Get attachment by product id
     * @param $productId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachmentsByProductId($productId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable($this->getMainTable()), $this->getIdFieldName())
            ->where('product_id = ?', (int) $productId);

        return $adapter->fetchCol($select);
    }

    /**
     * Filter attachment collection by product id
     * @param \Mageprince\Productattach\Model\ResourceModel\Productattach\Collection $collection
     * @param int $productId
     * @return \Mageprince\Productattach\Model\ResourceModel\Productattach\Collection $collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterAttachmentsByProductId($collection, $productId)
    {
        $collection->join(
            ['rel' => $this->getMainTable()],
            'main_table.productattach_id = rel.productattach_id'
        );

        $collection->getSelect()->where('product_id='.$productId);

        return $collection;
    }
}
