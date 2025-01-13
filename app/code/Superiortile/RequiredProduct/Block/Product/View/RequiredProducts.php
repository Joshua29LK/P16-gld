<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Block\Product\View;

use Magento\Catalog\Block\Product\View\Attributes;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template\Context;
use Superiortile\RequiredProduct\Helper\Data;

/**
 * Class Superiortile\RequiredProduct\Block\Product\View\RequiredProducts
 */
class RequiredProducts extends Attributes
{
    /**
     * @var string
     */
    protected $productListTemplate = 'Superiortile_RequiredProduct::product/list/items.phtml';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $collections;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Image $image
     * @param Data $helper
     * @param Context $context
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Image $image,
        Data                   $helper,
        Context                $context,
        Registry               $registry,
        PriceCurrencyInterface $priceCurrency,
        array                  $data = []
    ) {
        $this->serializer = $serializer;
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $image;
        $this->helper = $helper;
        parent::__construct($context, $registry, $priceCurrency, $data);
    }

    /**
     * Get Required Products Collection
     *
     * @param  int $linkTypeId
     * @return Collection
     */
    public function getRequiredCollection($linkTypeId)
    {
        $product = $this->getProduct();
        if (!isset($this->collections[$linkTypeId])) {
            $this->collections[$linkTypeId] = $product->getLinkInstance()
                ->setLinkTypeId($linkTypeId)
                ->getProductCollection()
                ->addAttributeToFilter('visibility', ['in' => $this->getVisibleInSiteIds()])
                ->addAttributeToSelect('*')
                ->setProduct($product)
                ->setIsStrongMode();
        }

        return $this->collections[$linkTypeId];
    }

    /**
     * Get Required Collection Data Json
     *
     * @param  int $linkTypeId
     * @return string
     */
    public function getRequiredCollectionDataJson($linkTypeId)
    {
        $collection = $this->getRequiredCollection($linkTypeId)->load();
        $result = [];

        /** @var Product $product */
        foreach ($collection as $product) {
            $result[$product->getId()] = [
                'id' => $product->getId(),
                'imageUrl' => $this->imageHelper->init($product, 'product_small_image')->getUrl(),
                'name' => $product->getName(),
                'price' => $product->getFinalPrice(),
                'formattedPrice' => $product->getFormattedPrice(),
                'productUrl' => $product->getProductUrl(),
                'regularPrice' => (float) $product->getPrice(),
                'attributes' => $this->getAttributes($product)
            ];
        }

        return $this->serializer->serialize($result);
    }

    /**
     * Get attributes show in block selected required product
     *
     * @param Product $product
     * @return array[]
     */
    protected function getAttributes($product)
    {
        $attrs = ['finish'];
        $result = [];

        foreach ($attrs as $attrCode) {
            $attribute = $product->getResource()->getAttribute($attrCode);
            $attributeLabel = $attribute ? $attribute->getStoreLabel() : '';
            $value = $attribute ? $attribute->getFrontend()->getValue($product) : '';
            if ($value && $attributeLabel) {
                $result[$attrCode] = [
                    'label' => $attributeLabel,
                    'value' => $value,
                    'attribute_code' => $attrCode
                ];
            }
        }

        $result['price'] = [
            'label' => __('Price'),
            'value' => $product->getFormattedPrice(),
            'attribute_code' => 'price'
        ];

        return $result;
    }

    /**
     * Get Collection Name
     *
     * @param  int $linkTypeId
     * @return string|null
     */
    public function getCollectionName($linkTypeId)
    {
        $product = $this->getProduct();
        $attributeCode = $this->helper->getCollectionNameAttributeCodeByTypeId($linkTypeId);
        $attribute = $attributeCode ? $product->getCustomAttribute($attributeCode) : null;

        return $attribute ? $attribute->getValue() : null;
    }

    /**
     * Get Collection Tooltip
     *
     * @param  int $linkTypeId
     * @return string|null
     */
    public function getCollectionTooltip($linkTypeId)
    {
        $product = $this->getProduct();
        $attributeCode = $this->helper->getTooltipAttributeCodeByTypeId($linkTypeId);

        $attribute = $attributeCode ? $product->getCustomAttribute($attributeCode) : null;

        return $attribute ? $attribute->getValue() : null;
    }

    /**
     * Get Product List Html
     *
     * @param  int $linkTypeId
     * @return string
     * @throws LocalizedException
     */
    public function getProductListHtml($linkTypeId)
    {
        $collection = $this->getRequiredCollection($linkTypeId);

        return $this->getLayout()
            ->createBlock(Items::class)
            ->setLinkTypeId($this->getLinkTypeId())
            ->setProductCollection($collection)
            ->setTemplate($this->productListTemplate)
            ->toHtml();
    }

    /**
     * Get Slide Configs
     *
     * @return string
     */
    public function getSlideConfigsJson()
    {
        $configs = [
            'items' => '4',
            'loop' => false,
            'nav' => true,
            'dots' => true,
            'wrapperSelectorClass' => '.products-wrapper',
            'responsive' => [
                '0' => [
                    'items' => '1'
                ],
                '480' => [
                    'items' => '2'
                ],
                '768' => [
                    'items' => '3'
                ],
                '1025' => [
                    'items' => '4'
                ],
            ]
        ];

        return $this->serializer->serialize($configs);
    }

    /**
     * Return visible site ids
     *
     * @return array
     */
    private function getVisibleInSiteIds()
    {
        return [
            Visibility::VISIBILITY_IN_SEARCH,
            Visibility::VISIBILITY_IN_CATALOG,
            Visibility::VISIBILITY_BOTH
        ];
    }
}
