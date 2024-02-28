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
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomOptionImage\Plugin\Model\ResourceModel\Product;

class Option
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Bss\CustomOptionImage\Helper\ModuleConfig
     */
    protected $config;

    /**
     * Construct.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Bss\CustomOptionImage\Helper\ModuleConfig $config
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Bss\CustomOptionImage\Helper\ModuleConfig $config
    ) {
        $this->resource = $resource;
        $this->config = $config;
    }

    /**
     * Duplicate COI image after Save & Duplicate product.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option $subject
     * @param \Magento\Catalog\Model\Product\Option $result
     * @param \Magento\Catalog\Model\Product\Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return \Magento\Catalog\Model\Product\Option
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDuplicate(
        \Magento\Catalog\Model\ResourceModel\Product\Option $subject,
        $result,
        $object,
        $oldProductId,
        $newProductId
    ) {
        if (!$this->config->isModuleEnable()) {
            return $result;
        }

        $connection = $this->resource->getConnection();
        $oldOptionTypeId = $this->getOptionTypeId($connection, $oldProductId);
        $newOptionTypeId = $oldOptionTypeId ? $this->getOptionTypeId($connection, $newProductId) : [];

        foreach ($newOptionTypeId as $key => $newTypeId) {
            // COI image
            $table = $this->resource->getTableName('bss_catalog_product_option_type_image');

            $select = $connection->select()->from(
                $table,
                [new \Zend_Db_Expr($newTypeId), 'image_url', 'swatch_image_url']
            )->where(
                'option_type_id = ?',
                $oldOptionTypeId[$key]
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_type_id', 'image_url', 'swatch_image_url'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);
        }

        return $result;
    }

    /**
     * Get all option type id by product id
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param int $productId
     * @return array
     */
    public function getOptionTypeId($connection, $productId)
    {
        $select = $connection->select()
            ->from($this->resource->getTableName('catalog_product_option'), ['option_id'])
            ->where('product_id = ?', $productId);

        $optionData = $connection->fetchAll($select);
        $optionId = array_column($optionData, "option_id");

        if (!$optionId) {
            return [];
        }

        $select = $connection->select()
            ->from($this->resource->getTableName('catalog_product_option_type_value'), ['option_type_id'])
            ->where('option_id IN (?)', $optionId);

        return array_column($connection->fetchAll($select), "option_type_id");
    }
}
