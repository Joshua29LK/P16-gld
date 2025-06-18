<?php
namespace Bss\RenderServerPriceCtm\Pricing\Render;

use Bss\RenderServerPriceCtm\CustomerData\ConfigurableItem;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;

class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{
    protected $_isScopePrivate = true;

    /**
     * @var ConfigurableItem
     */
    protected $configurableItem;

    /**
     * @param ConfigurableItem $configurableItem
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param SalableResolverInterface $salableResolver
     * @param MinimalPriceCalculatorInterface $minimalPriceCalculator
     * @param ConfigurableOptionsProviderInterface $configurableOptionsProvider
     * @param array $data
     */
    public function __construct(
        ConfigurableItem $configurableItem,
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        SalableResolverInterface $salableResolver,
        MinimalPriceCalculatorInterface $minimalPriceCalculator,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        array $data = []
    ) {
        $this->configurableItem = $configurableItem;
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $salableResolver,
            $minimalPriceCalculator,
            $configurableOptionsProvider,
            $data
        );
    }

    /**
     * Disable block cache
     *
     * @return null
     */
    public function getCacheLifetime()
    {
        return null;
    }

    /**
     * Get child product selected
     *
     * @return ProductInterface|Product|null
     * @throws NoSuchEntityException
     */
    public function getSelectedChild()
    {
        return $this->configurableItem->getSelectedChild();
    }

    /**
     * To html
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->getRequest()->getParam("load_html") == 1) {
            $this->getRequest()->setParam("load_html", 0);
            return $this->_toHtml();
        }
        return parent::toHtml();
    }
}