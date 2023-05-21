<?php
namespace Bss\FixError\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;

class CartItemProcessor extends \Magento\ConfigurableProduct\Model\Quote\Item\CartItemProcessor
{
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Quote\Model\Quote\ProductOptionFactory $productOptionFactory
     * @param \Magento\Quote\Api\Data\ProductOptionExtensionFactory $extensionFactory
     * @param \Magento\ConfigurableProduct\Model\Quote\Item\ConfigurableItemOptionValueFactory $itemOptionValueFactory
     * @param Json $serializer
     */
    public function __construct(
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Quote\Model\Quote\ProductOptionFactory $productOptionFactory,
        \Magento\Quote\Api\Data\ProductOptionExtensionFactory $extensionFactory,
        \Magento\ConfigurableProduct\Model\Quote\Item\ConfigurableItemOptionValueFactory $itemOptionValueFactory,
        Json $serializer = null
    ) {
        parent::__construct(
            $objectFactory,
            $productOptionFactory,
            $extensionFactory,
            $itemOptionValueFactory,
            $serializer
        );

        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @inheritdoc
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        $attributesOption = $cartItem->getProduct()
            ->getCustomOption('attributes');
        if (!$attributesOption) {
            return $cartItem;
        }
        $selectedConfigurableOptions = $this->serializer->unserialize($attributesOption->getValue());

        if (is_array($selectedConfigurableOptions)) {
            $configurableOptions = [];
            foreach ($selectedConfigurableOptions as $optionId => $optionValue) {
                /** @var ConfigurableItemOptionValueInterface $option */
                $option = $this->itemOptionValueFactory->create();
                $option->setOptionId($optionId);
                $option->setOptionValue($optionValue);
                $configurableOptions[] = $option;
            }

            $productOption = $cartItem->getProductOption()
                ? $cartItem->getProductOption()
                : $this->productOptionFactory->create();

            /** @var  ProductOptionExtensionInterface $extensibleAttribute */
            $extensibleAttribute = $productOption->getExtensionAttributes()
                ? $productOption->getExtensionAttributes()
                : $this->extensionFactory->create();

            $extensibleAttribute->setConfigurableItemOptions($configurableOptions);
            $productOption->setExtensionAttributes($extensibleAttribute);
            $cartItem->setProductOption($productOption);
        }

        return $cartItem;
    }
}
