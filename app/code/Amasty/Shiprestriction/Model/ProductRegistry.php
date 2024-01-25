<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model;

class ProductRegistry
{
    /**
     * @var array
     */
    protected $products = [];

    /**
     * @var array
     */
    private $productValidationData = [];

    /**
     * @var string[]
     */
    private $restrictedProducts = [];

    /**
     * @param $name
     * @return $this
     */
    public function addProduct($name)
    {
        if (!in_array($name, $this->products)) {
            $this->products [] = $name;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearProducts()
    {
        $this->products = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $products
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->products = $products;

        return $this;
    }

    public function clearProductValidationData(): void
    {
        $this->productValidationData = [];
    }

    /**
     * @param array $data ['sku' => ['name' => {name}, 'validation_result' => bool], ...]
     * @return void
     */
    public function addProductValidationData(array $data): void
    {
        foreach ($data as $sku => $validationData) {
            if (!isset($this->productValidationData[$sku])
                || $this->productValidationData[$sku]['validation_result'] === false) {
                $this->productValidationData[$sku] = $validationData;
            }
        }
    }

    /**
     * @return array [{sku} => ['name' => {name}, 'validation_result' => bool], ...]
     */
    public function getProductValidationData(): array
    {
        return $this->productValidationData;
    }

    public function clearRestrictedProducts(): void
    {
        $this->restrictedProducts = [];
    }

    /**
     * @param string[] $products
     * @return void
     */
    public function setRestrictedProducts(array $products): void
    {
        $this->restrictedProducts = $products;
    }

    /**
     * @return string[]
     */
    public function getRestrictedProducts(): array
    {
        return $this->restrictedProducts;
    }
}
