<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Plugin;

use Balticode\CategoryConfigurator\Model\Validator\StepsData;

class GlassShapesProvider
{
    /**
     * BaseFieldsProvider constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->resourceProduct = $resourceProduct;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Balticode\CategoryConfigurator\Model\Product\GlassShapesProvider $subject
     * @param array $result
     * @param array $glassShapes
     * @return array
     */
    public function afterAppendDefaultGlassShapesDimensions(
        \Balticode\CategoryConfigurator\Model\Product\GlassShapesProvider $subject,
        $result,
        $glassShapes
    ){
        if (!is_array($glassShapes)) {
            return $glassShapes;
        }

        $store = $this->storeManager->getStore();
        
        foreach ($result as $key => $value) {
            $height = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'glass_shape_height', $store);
            $width = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'glass_shape_width', $store);
            $glassShapesMinimumHeight = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'min_glass_shape_height', $store);
            $glassShapesMinimumWidth = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'min_glass_shape_width', $store);
            $glassShapesMaximumHeight = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'max_glass_shape_height', $store);
            $glassShapesMaximumWidth = $this->resourceProduct->getAttributeRawValue($value['product_id'], 'max_glass_shape_width', $store);

            $result[$key]['glassShapesMinimumWidth'] = !empty($glassShapesMinimumWidth)? $glassShapesMinimumWidth: '';
            $result[$key]['glassShapesMaximumWidth'] = !empty($glassShapesMaximumWidth)? $glassShapesMaximumWidth: '';
            $result[$key]['glassShapesMinimumHeight'] = !empty($glassShapesMinimumHeight)? $glassShapesMinimumHeight: '';
            $result[$key]['glassShapesMaximumHeight'] = !empty($glassShapesMaximumHeight)? $glassShapesMaximumHeight: '';

            if (!empty($height)) {
                $result[$key][StepsData::ARRAY_INDEX_HEIGHT] = $height ;
            }
            
            if (!empty($width)) {
                $result[$key][StepsData::ARRAY_INDEX_WIDTH] = $width;
                $result[$key][StepsData::ARRAY_INDEX_TOP_WIDTH] = $width;
                $result[$key][StepsData::ARRAY_INDEX_BOTTOM_WIDTH] = $width;
            }
        }

        return $result;
    }
}
