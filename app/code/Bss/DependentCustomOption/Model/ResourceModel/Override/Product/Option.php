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
namespace Bss\DependentCustomOption\Model\ResourceModel\Override\Product;

use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Option extends \Magento\Catalog\Model\ResourceModel\Product\Option
{
    /**
     * @var \Bss\DependentCustomOption\Helper\DependOption
     */
    protected $dependHelper;

    /**
     * Define main table and initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->dependHelper = ObjectManager::getInstance()->create(
            \Bss\DependentCustomOption\Helper\DependOption::class
        );
    }

    /**
     * Duplicate custom options for product
     *
     * @param \Magento\Catalog\Model\Product\Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return \Magento\Catalog\Model\Product\Option
     * @codingStandardsIgnoreStart
     */
    public function duplicate(\Magento\Catalog\Model\Product\Option $object, $oldProductId, $newProductId)
    {
        $connection = $this->getConnection();

        $optionsCond = [];
        $optionsData = [];

        // read and prepare original product options
        $select = $connection->select()->from(
            $this->getTable('catalog_product_option')
        )->where(
            'product_id = ?',
            $oldProductId
        );

        $query = $connection->query($select);

        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $connection->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $connection->lastInsertId($this->getMainTable());
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            //set depend option for bss_depend_co table
            $data = $this->dependHelper->returnDependData(
                $oldOptionId,
                $newOptionId,
                $newProductId,
                'option_id'
            );
            $this->dependHelper->saveDependOption($newProductId, $data, null);

            // title
            $table = $this->getTable('catalog_product_option_title');

            $select = $this->getConnection()->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'title']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'title'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            // price
            $table = $this->getTable('catalog_product_option_price');

            $select = $connection->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'price', 'price_type']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'price', 'price_type'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }
        // @codingStandardsIgnoreEnd
        return $object;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\MetadataPool::class);
        }
        return $this->metadataPool;
    }
}
