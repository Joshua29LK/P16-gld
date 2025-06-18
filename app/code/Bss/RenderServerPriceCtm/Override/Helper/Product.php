<?php
namespace Bss\RenderServerPriceCtm\Override\Helper;

class Product extends \Magmodules\Channable\Helper\Product
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $simple
     * @param                                $config
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getProductUrl($product, $simple, $config)
    {
        $url = null;
        if ($requestPath = $product->getRequestPath()) {
            $url = $product->getProductUrl();
        } else {
            $url = $config['base_url'] . 'catalog/product/view/id/' . $product->getEntityId();
        }
        if (!empty($config['utm_code'])) {
            if ($config['utm_code'][0] != '?') {
                $url .= '?' . $config['utm_code'];
            } else {
                $url .= $config['utm_code'];
            }
        }
        if (!empty($simple)) {
            if ($product->getTypeId() == 'configurable') {
                if (isset($config['filters']['link']['configurable'])) {
                    if ($config['filters']['link']['configurable'] == 2) {
                        $options = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
                        foreach ($options as $option) {
                            if ($id = $simple->getResource()->getAttributeRawValue(
                                $simple->getId(),
                                $option['attribute_code'],
                                $config['store_id']
                            )
                            ) {
                                $urlExtra[] = 'opt_' . $option['attribute_id'] . '=' . $id;
                            }
                        }
                    }
                }
            }
            if (!empty($urlExtra) && !empty($url)) {
                if (strpos($url, '?') === false) {
                    $url .= '?';
                }
                $url = $url . '~' . implode('&', $urlExtra);
            }
        }

        return $url;
    }
}