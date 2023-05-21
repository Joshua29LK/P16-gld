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
 * @package    Bss_CustomOptionCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomOptionCore\Block\Options\Type;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

class Select extends \Magento\Catalog\Block\Product\View\Options\Type\Select
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Select constructor.
     * @param TemplateContext $context
     * @param PricingHelper $pricingHelper
     * @param CatalogHelper $catalogData
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        PricingHelper $pricingHelper,
        CatalogHelper $catalogData,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $pricingHelper, $catalogData, $data);
    }

    /**
     * GetBssCustomOptionBlock
     *
     * @param string $place
     * @return string
     * @throws LocalizedException
     */
    public function getBssCustomOptionBlock($place)
    {
        $childObject = $this->dataObjectFactory->create();

        $this->_eventManager->dispatch(
            'bss_custom_options_render_select_' . $place,
            ['child' => $childObject]
        );
        $blocks = $childObject->getData() ?: [];
        $output = '';

        foreach ($blocks as $childBlock) {
            $block = $this->getLayout()->createBlock($childBlock);
            $block->setProduct($this->getProduct())->setOption($this->getOption());
            $output .= $block->toHtml();
        }
        return $output;
    }
}
