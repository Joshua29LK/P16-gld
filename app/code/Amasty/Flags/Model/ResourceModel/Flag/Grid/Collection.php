<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Model\ResourceModel\Flag\Grid;

use Amasty\Flags\Model\Flag;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Amasty\Flags\Model\ResourceModel\Flag\Collection as FlagCollection;

/**
 * @method Flag[] getItems()
 */
class Collection extends FlagCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;
    /**
     * @var Flag
     */
    private $flagSingleton;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;
    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    private $shippingConfig;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        Flag $flagSingleton,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingConfig,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->flagSingleton = $flagSingleton;
        $this->scopeConfig = $scopeConfig;
        $this->orderConfig = $orderConfig;
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    protected function _afterLoad()
    {
        $paymentMethods = $this->scopeConfig->getValue('payment');
        $shippingMethods = $this->shippingConfig->toOptionArray(true);
        $orderStatuses = $this->orderConfig->getStatuses();
        $shippingCarriers = [];

        foreach ($shippingMethods as $shippingMethod) {
            if (is_array($shippingMethod['value'])) {
                foreach ($shippingMethod['value'] as $carrier) {
                    $shippingCarriers[$carrier['value']] = $carrier['label'];
                }
            }
        }

        foreach ($this->getItems() as $item) {
            $item
                ->setData('image_name_src', $this->flagSingleton->getImageUrl($item))
                ->setData('image_name_alt', $item->getName());

            // payment
            $appliedMethods = explode(',', $item->getApplyPayment());
            $output = [];
            foreach ($appliedMethods as $code) {
                if (isset($paymentMethods[$code])) {
                    if (isset($paymentMethods[$code]['title'])) {
                        $output[] = $paymentMethods[$code]['title'];
                    } else {
                        $output[] = $code;
                    }
                }
            }
            $item->setApplyPayment(implode(', ', $output));

            //shipping
            $appliedMethods = explode(',', $item->getApplyShipping());
            $output = [];
            foreach ($appliedMethods as $code) {
                if (isset($shippingCarriers[$code])) {
                    $output[] = $shippingCarriers[$code];
                }
            }
            $item->setApplyShipping(implode(', ', $output));

            //status
            $appliedStatuses = explode(',', $item->getApplyStatus());
            $output = [];
            foreach ($appliedStatuses as $code) {
                if (isset($orderStatuses[$code])) {
                    $output[] = $orderStatuses[$code];
                }
            }
            $item->setApplyStatus(implode(', ', $output));
        }

        return parent::_afterLoad();
    }
}
