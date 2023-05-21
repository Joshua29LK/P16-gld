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
namespace Bss\DependentCustomOption\Model\ResourceModel\Override\Product\Option;

use Magento\Catalog\Model\Product\Option\Value as OptionValue;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\FormatInterface;

class Value extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value
{
    /**
     * @var FormatInterface
     */
    private $localeFormat;

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
        $this->_init('catalog_product_option_type_value', 'option_type_id');
        $this->dependHelper = ObjectManager::getInstance()->create(
            \Bss\DependentCustomOption\Helper\DependOption::class
        );
    }

    /**
     * Duplicate product options value
     *
     * @param OptionValue $object
     * @param int $oldOptionId
     * @param int $newOptionId
     * @return OptionValue
     * @codingStandardsIgnoreStart
     */
    public function duplicate(OptionValue $object, $oldOptionId, $newOptionId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->where('option_id = ?', $oldOptionId);
        $valueData = $connection->fetchAll($select);

        $valueCond = [];

        foreach ($valueData as $data) {
            $optionTypeId = $data[$this->getIdFieldName()];
            unset($data[$this->getIdFieldName()]);
            $data['option_id'] = $newOptionId;

            $connection->insert($this->getMainTable(), $data);
            $valueCond[$optionTypeId] = $connection->lastInsertId($this->getMainTable());
        }
        // get product Id from option
        $selectOption = $connection->select()->from(
            $this->getTable('catalog_product_option'),
            ['product_id']
        )->where(
            'option_id = ?',
            $newOptionId
        );
        $productId = $connection->fetchOne($selectOption);
        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            //set option_type_id for bss_depend_co table
            $data = $this->dependHelper->returnDependData(
                $oldTypeId,
                $newTypeId,
                $productId,
                'option_type_id'
            );
            $this->dependHelper->saveDependOption($productId, $data, null);

            // price
            $priceTable = $this->getTable('catalog_product_option_type_price');
            $columns = [new \Zend_Db_Expr($newTypeId), 'store_id', 'price', 'price_type'];

            $select = $connection->select()->from(
                $priceTable,
                []
            )->where(
                'option_type_id = ?',
                $oldTypeId
            )->columns(
                $columns
            );
            $insertSelect = $connection->insertFromSelect(
                $select,
                $priceTable,
                ['option_type_id', 'store_id', 'price', 'price_type']
            );
            $connection->query($insertSelect);

            // title
            $titleTable = $this->getTable('catalog_product_option_type_title');
            $columns = [new \Zend_Db_Expr($newTypeId), 'store_id', 'title'];

            $select = $this->getConnection()->select()->from(
                $titleTable,
                []
            )->where(
                'option_type_id = ?',
                $oldTypeId
            )->columns(
                $columns
            );
            $insertSelect = $connection->insertFromSelect(
                $select,
                $titleTable,
                ['option_type_id', 'store_id', 'title']
            );
            $connection->query($insertSelect);
        }
        // @codingStandardsIgnoreEnd

        return $object;
    }

    /**
     * Get FormatInterface to convert price from string to number format
     *
     * @return FormatInterface
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getLocaleFormatter()
    {
        if ($this->localeFormat === null) {
            $this->localeFormat = ObjectManager::getInstance()
                ->get(FormatInterface::class);
        }
        return $this->localeFormat;
    }
}
