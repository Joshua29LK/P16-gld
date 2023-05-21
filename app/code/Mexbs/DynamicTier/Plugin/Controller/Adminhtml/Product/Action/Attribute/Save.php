<?php
namespace Mexbs\DynamicTier\Plugin\Controller\Adminhtml\Product\Action\Attribute;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\GroupManagementInterface;

class Save{
    private $attributeHelper;
    private $objectManager;
    private $tierpriceResource;
    private $metadataPool;
    private $productPriceIndexerProcessor;
    private $typeList;
    private $groupManagement;

    public function __construct(
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice $tierpriceResource,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        GroupManagementInterface $groupManagement
    ) {
        $this->attributeHelper = $attributeHelper;
        $this->objectManager = $objectManager;
        $this->tierpriceResource = $tierpriceResource;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->typeList = $typeList;
        $this->groupManagement = $groupManagement;
    }

    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }

    /**
     * @return bool
     */
    protected function _validatedProducts(){
        $productIds = $this->attributeHelper->getProductIds();
        if (!is_array($productIds)) {
            return false;
        } elseif (!$this->objectManager->create('Magento\Catalog\Model\Product')->isProductsHasSku($productIds)) {
            return false;
        }
        return true;
    }

    public function beforeExecute(
        \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save $subject
    ){
        if($subject->getRequest()->getParam('toggle_tier_price') != 'on'){
            return [];
        }

        $tierPriceData = $subject->getRequest()->getParam('tier_price', []);

        if(is_array($tierPriceData)
            && $this->_validatedProducts()){

            $productIds = $this->attributeHelper->getProductIds();
            $allcustomersGroupId = $this->groupManagement->getAllCustomersGroup()->getId();

            foreach($productIds as $productId){
                $this->tierpriceResource->deletePriceData($productId);

                foreach($tierPriceData as $dataRow){
                    $useForAllGroups = $dataRow['cust_group'] == $allcustomersGroupId;
                    $customerGroupId = !$useForAllGroups ? $dataRow['cust_group'] : 0;

                    $priceValue = $dataRow['price'];
                    $discountValue = 0;

                    if($dataRow['price_type'] == "discount"){
                        $priceValue = 0;
                        $discountValue = $dataRow['price'];
                    }

                    $price = new \Magento\Framework\DataObject([
                        'website_id' => $dataRow['website_id'],
                        'all_groups' => $useForAllGroups ? 1 : 0,
                        'customer_group_id' => $customerGroupId,
                        'qty' => $dataRow['price_qty'],
                        'value' => $priceValue,
                        'percentage_value' => $discountValue
                    ]);
                    $price->setData(
                        $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField(),
                        $productId
                    );
                    $this->tierpriceResource->savePriceData($price);
                }
            }

            $this->productPriceIndexerProcessor->reindexList($productIds);
            $this->typeList->invalidate(['block_html', 'full_page']);
        }

        return [];
    }
}