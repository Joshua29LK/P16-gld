<?php
namespace Bss\RenderServerPriceCtm\CustomerData;

use Magento\Checkout\CustomerData\DefaultItem;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Bss\RenderServerPriceCtm\Helper\Data;

class ConfigurableItem extends DefaultItem
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Msrp\Helper\Data $msrpHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\Escaper|null $escaper
     * @param ItemResolverInterface|null $itemResolver
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        Data $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Msrp\Helper\Data $msrpHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\Escaper $escaper = null,
        ItemResolverInterface $itemResolver = null
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        parent::__construct(
            $imageHelper,
            $msrpHelper,
            $urlBuilder,
            $configurationPool,
            $checkoutHelper,
            $escaper,
            $itemResolver
        );
    }

    /**
     * @param Item $item
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItemData(Item $item)
    {
        return $this->getProduct();
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|null
     */
    public function getSelectedChild()
    {
        $action = $this->request->getFullActionName();
        $productId = $this->request->getParam('id');
        if ($action == 'catalog_product_view') {
            $selectedParams = $this->helper->getChildParams();
            if (!$selectedParams) {
                return null;
            }
            $product = $this->productRepository->getById($productId);
            parse_str($selectedParams, $paramsArray);

            $cleanParams = [];
            foreach ($paramsArray as $key => $value) {
                if (strpos($key, 'opt_') === 0) {
                    $cleanKey = substr($key, 4);
                } else {
                    $cleanKey = $key;
                }
                $cleanParams[$cleanKey] = $value;
            }

            $productTypeInstance = $product->getTypeInstance();
            $child = $productTypeInstance->getProductByAttributes($cleanParams, $product);
            if ($child && ($child instanceof \Magento\Catalog\Model\Product || $child instanceof \Magento\Catalog\Api\Data\ProductInterface) && $child->getSku()) {
                return $child;
            }
        }
        return null;
    }
}