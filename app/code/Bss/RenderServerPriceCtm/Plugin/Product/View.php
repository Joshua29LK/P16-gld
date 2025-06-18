<?php
namespace Bss\RenderServerPriceCtm\Plugin\Product;

use Bss\RenderServerPriceCtm\CustomerData\ConfigurableItem;
use Magento\Framework\Serialize\Serializer\Json;

class View
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @var ConfigurableItem
     */
    protected $configurableItem;

    /**
     * @var \Bss\RenderServerPriceCtm\Helper\Data
     */
    protected $helper;

    /**
     * @param Json $json
     * @param ConfigurableItem $configurableItem
     */
    public function __construct(
        Json $json,
        \Bss\RenderServerPriceCtm\Helper\Data $helper,
        ConfigurableItem $configurableItem
    ) {
        $this->json = $json;
        $this->helper = $helper;
        $this->configurableItem = $configurableItem;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetJsonConfig(
        $subject,
        $result
    ) {
        $childProduct = $this->configurableItem->getSelectedChild();
        if (!$this->helper->isChild()) {
            return $result;
        }
        $priceInfo = $childProduct->getPriceInfo();
        $config = $this->json->unserialize($result);
        $config['prices']['baseOldPrice']['amount'] = $priceInfo->getPrice('regular_price')->getAmount()->getBaseAmount() * 1;
        $config['prices']['oldPrice']['amount'] = $priceInfo->getPrice('regular_price')->getAmount()->getValue() * 1;
        $config['prices']['basePrice']['amount'] = $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount() * 1;
        $config['prices']['finalPrice']['amount'] = $priceInfo->getPrice('final_price')->getAmount()->getValue() * 1;
        return $this->json->serialize($config);
    }
}
