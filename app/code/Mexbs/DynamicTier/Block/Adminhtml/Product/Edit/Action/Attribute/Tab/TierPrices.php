<?php
namespace Mexbs\DynamicTier\Block\Adminhtml\Product\Edit\Action\Attribute\Tab;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\Product;

class TierPrices extends \Magento\Backend\Block\Widget
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $groupRepository;
    private $searchCriteriaBuilder;
    private $eavConfig;
    private $catalogHelper;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    ){
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->eavConfig = $eavConfig;
        $this->catalogHelper = $catalogHelper;

        parent::__construct($context, $data);
    }

    public function getTabLabel(){
        return "Tier Prices";
    }

    public function getTabTitle(){
        return "Tier Prices";
    }

    public function canShowTab(){
        return true;
    }

    public function isHidden(){
        return false;
    }

    public function getWebsitesOptionsArray(){
        $websites = [
            [
                'label' => __('All Websites'),
                'value' => 0,
            ]
        ];

        $tierPriceAttribute = $this->eavConfig->getAttribute(Product::ENTITY, 'tier_price');
        if(!$tierPriceAttribute->isScopeGlobal() && !$this->catalogHelper->isPriceGlobal()){
            $websitesList = $this->_storeManager->getWebsites();
            foreach ($websitesList as $website) {
                /** @var \Magento\Store\Model\Website $website */
                $websites[] = [
                    'label' => $website->getName(),
                    'value' => $website->getId(),
                ];
            }
        }

        return $websites;
    }

    public function getCustomerGroupsOptionsArray()
    {
        $customerGroups = [
            [
                'label' => __('ALL GROUPS'),
                'value' => GroupInterface::CUST_GROUP_ALL,
            ]
        ];

        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                'label' => $group->getCode(),
                'value' => $group->getId(),
            ];
        }

        return $customerGroups;
    }
}