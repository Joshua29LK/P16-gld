<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;

class BaseFieldsProvider
{
    /**
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->resource = $resource;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Balticode\CategoryConfigurator\Model\Product\BaseFieldsProvider $subject
     * @param array $result
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array|null
     */
    public function afterGetBaseProductFields(\Balticode\CategoryConfigurator\Model\Product\BaseFieldsProvider $subject, $result, $product) 
    {
        if ($product->getTypeId() == 'bundle') {
            $result['price'] = $product->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue('tax');
        }

        $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'status'
            )->getId();

        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        $result['product_sku'] = $product->getSku();

        // check product in stock/out of stock

        $connection = $this->resource->getConnection();

        if ($product->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE ||
            $product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE ||
            $product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE ) {
            $typeInstance = $product->getTypeInstance();
            $childrenIds = $typeInstance->getChildrenIds($product->getId(), true);
            if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $childrenIds = array_values($childrenIds[0]);
            }
            if ($product->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE || $product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
                $childrenIds = array_merge(...array_values($childrenIds));
            }
           
            if (!count($childrenIds)) {
                return null;
            }

            $select = $connection->select()
                ->from(
                    ['inventory_in_stock' => 'cataloginventory_stock_item'],
                    ['is_in_stock']
                )->joinInner(
                    ['mcpei' => 'catalog_product_entity_int'],
                    'inventory_in_stock.product_id = mcpei.entity_id'
                    . ' AND mcpei.attribute_id = ' . $statusAttributeId
                    . ' AND mcpei.value = ' . \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                    []
                )->where('(inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=1 AND inventory_in_stock.is_in_stock=1) OR (inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=0) OR inventory_in_stock.use_config_manage_stock = 1')
                ->where('inventory_in_stock.product_id IN(?)', $childrenIds);
            $data = $connection->fetchAll($select);
            
            if (count($childrenIds) > count($data)) {
                return null;
            }
        } else {
            if ($product instanceof ProductInterface) {            
                $select = $connection->select()
                    ->from(
                        ['inventory_in_stock' => 'cataloginventory_stock_item'],
                        ['is_in_stock']
                    )->joinInner(
                        ['mcpei' => 'catalog_product_entity_int'],
                        'inventory_in_stock.product_id = mcpei.entity_id'
                        . ' AND mcpei.attribute_id = ' . $statusAttributeId
                        . ' AND mcpei.value = ' . \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                        []
                    )->where('(inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=1 AND inventory_in_stock.is_in_stock=1) OR (inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=0) OR inventory_in_stock.use_config_manage_stock = 1')
                    ->where('inventory_in_stock.product_id = ?', $product->getId());
                $data = $connection->fetchAll($select);
                if (!count($data)) {
                    return null;
                }
            }
        }

        return $result;
    }
}
