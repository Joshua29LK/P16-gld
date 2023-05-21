<?php
/**
 * Created by PhpStorm.
 * User: Thomas Dreiling (thomas.dreiling@future-dreams.de)
 * Date: 13.08.2018
 * Time: 12:54
 */

namespace Fd\UrlPathFix\CatalogUrlRewrite\Model;

class ProductUrlPathGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
{
    /**
     * Ignore attribute url_path. Always use url_key instead.
     *
     * @inheritdoc
     */
    public function getUrlPath($product, $category = null)
    {
        $path = $product->getUrlKey()
            ? $this->prepareProductUrlKey($product)
            : $this->prepareProductDefaultUrlKey($product);
        return $category === null
            ? $path
            : $this->categoryUrlPathGenerator->getUrlPath($category) . '/' . $path;
    }
}
