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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Model;

use Bss\Faqs\Helper\ModuleConfig;
use Bss\Faqs\Model\ResourceModel\FaqCategory\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Faqs extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'faqs';

    /**
     * @var \Bss\Faqs\Helper\ModuleConfig
     */
    private $moduleHelper;

    /**
     * @var \Bss\Faqs\Model\ResourceModel\FaqCategory\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Bss\Faqs\Model\ResourceModel\FaqsVote\CollectionFactory
     */
    private $voteCollectionFactory;

    /**
     * @var FaqsVoteFactory
     */
    private $faqVoteFactory;
    /**
     * @var FaqsStoreFactory
     */
    private $faqsStoreFactory;

    /**
     * @param ModuleConfig $moduleHelper
     * @param CollectionFactory $categoryCollectionFactory
     * @param ResourceModel\FaqsVote\CollectionFactory $voteCollectionFactory
     * @param FaqsVoteFactory $faqVoteFactory
     * @param ResourceModel\FaqsStore\CollectionFactory $faqsStoreFactory
     * @param Context $context
     * @param Registry $registry
     * @param FilterManager $filter
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Bss\Faqs\Helper\ModuleConfig                               $moduleHelper,
        \Bss\Faqs\Model\ResourceModel\FaqCategory\CollectionFactory $categoryCollectionFactory,
        \Bss\Faqs\Model\ResourceModel\FaqsVote\CollectionFactory    $voteCollectionFactory,
        \Bss\Faqs\Model\FaqsVoteFactory                             $faqVoteFactory,
        \Bss\Faqs\Model\ResourceModel\FaqsStore\CollectionFactory   $faqsStoreFactory,
        \Magento\Framework\Model\Context                            $context,
        \Magento\Framework\Registry                                 $registry,
        \Magento\Framework\Filter\FilterManager                     $filter,
        \Magento\Framework\Model\ResourceModel\AbstractResource     $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb               $resourceCollection = null,
        array                                                       $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->voteCollectionFactory = $voteCollectionFactory;
        $this->faqVoteFactory = $faqVoteFactory;
        $this->faqsStoreFactory = $faqsStoreFactory;
        parent::__construct($context, $registry, $filter, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Bss\Faqs\Model\ResourceModel\Faqs::class);
    }

    /**
     * Before save
     *
     * @return \Magento\Framework\Model\AbstractModel|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->beforeSaveStore()
            ->beforeSaveTime()
            ->beforeSaveTag()
            ->beforeSaveVote();
    }

    /**
     * Validate Url
     *
     * @return bool|void
     * @throws LocalizedException
     */
    protected function validateUrl()
    {
        $id = $this->getId();
        $urlKey = $this->filter->translitUrl($this->getUrlKey());
        if ($id) {
            if ($urlKey) {
                $flag = true;
                foreach ($this->getStoreArray() as $storeId) {
                    $result = $this->faqsStoreFactory->create()
                        ->addFieldToFilter('faq_id', ['neq' => $id])
                        ->addFieldToFilter('store_id', $storeId)
                        ->addFieldToFilter('url_key', $urlKey);
                    if (!empty($result->getAllIds())) {
                        $flag = false;
                    }
                }
                if (!$flag) {
                    throw new LocalizedException(__("URL '%1' already exist", $this->getUrlKey()));
                }
            } else {
                $urlKey = $this->createUrlKey();
                if (!$urlKey) {
                    throw new LocalizedException(__("Can not save Url Key '%1'", $this->getUrlKey()));
                }
                $this->setUrlKey($urlKey);
            }
        } else {
            $flag = true;
            if ($urlKey) {
                foreach ($this->getStoreArray() as $storeId) {
                    $result = $this->faqsStoreFactory->create()
                        ->addFieldToFilter('store_id', $storeId)
                        ->addFieldToFilter('url_key', $urlKey);
                    if (!empty($result->getAllIds())) {
                        $flag = false;
                    }
                }
                if (!$flag) {
                    throw new LocalizedException(__("URL '%1' already exist", $this->getUrlKey()));
                }
            } else {
                $urlKey = $this->createUrlKey();
                if (!$urlKey) {
                    throw new LocalizedException(__("Can not save Url Key '%1'", $this->getUrlKey()));
                }
                $this->setUrlKey($urlKey);
            }
        }
        return true;
    }

    /**
     * Before save store
     *
     * @return $this
     */
    private function beforeSaveStore()
    {
        $storeIds = $this->getData('store_id');
        if (is_array($storeIds)) {
            $this->setData('store_id', implode(',', $storeIds));
        }
        return $this;
    }

    /**
     * Before save time
     *
     * @return $this
     */
    private function beforeSaveTime()
    {
        if ($this->getTime() == '') {
            $this->setTime($this->moduleHelper->date('Y-m-d'));
        }
        return $this;
    }

    /**
     * Before save tag
     *
     * @return $this
     */
    private function beforeSaveTag()
    {
        $result = $this->getTagArray();
        foreach ($result as $key => $tag) {
            if ($tag === '') {
                unset($result[$key]);
            }
        }
        if ($result) {
            $this->setTag(implode(',', $result));
        }
        return $this;
    }

    /**
     * Before save vote
     *
     * @return $this
     */
    private function beforeSaveVote()
    {
        if ($this->getUseRealVoteData() && $this->getId()) {
            $voteData = $this->voteCollectionFactory->create()->load($this->getId())->getVoteData();
            $this->setHelpfulVote($voteData['like']);
            $this->setUnhelpfulVote($voteData['unlike']);
        }
        return $this;
    }

    /**
     * After load
     *
     * @return AbstractModel|void
     */
    public function afterLoad()
    {
        parent::afterLoad();
        $this->afterLoadStore();
    }

    /**
     * After load Store
     */
    private function afterLoadStore()
    {
        $storeIds = $this->getData('store_id');
        if (!is_array($storeIds)) {
            if ($storeIds == "" || $storeIds == null) {
                $this->setData('store_id', []);
            } elseif (is_int($storeIds)) {
                $this->setData('store_id', [$storeIds]);
            } else {
                $this->setData('store_id', explode(',', $storeIds));
            }
        }
    }

    /**
     * Get View Data
     *
     * @param bool $hasLink
     * @param int $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getViewData($hasLink = true, $storeId = false)
    {
        if ($hasLink) {
            $relateds = [];
            $relatedIds = $this->getRelatedFaqId() ? explode(';', $this->getRelatedFaqId()) : [];
            $relatedFaqs = $this->getResourceCollection()->addFieldToFilter('faq_id', ['in' => $relatedIds]);
            if ($storeId) {
                $relatedFaqs->addFieldToFilter('store_id', ['finset' => [$storeId]]);
            }
            foreach ($relatedFaqs as $faq) {
                $relateds[] = $faq->getViewData(false);
            }
            $this->setRelatedFaqId($relateds);
        }
        if ($this->getIsShowFullAnswer()) {
            $this->setShortAnswer($this->moduleHelper->pageFilter($this->getAnswer()));
        }
        $this->setAnswer($this->moduleHelper->pageFilter($this->getAnswer()));
        $this->setTime($this->moduleHelper->date('Y M d', $this->getTime()));
        return $this->getData();
    }

    /**
     * Vote
     *
     * @param int $type
     * @param bool $isNew
     */
    public function vote($type, $isNew = false)
    {
        if ($type > 0) {
            $this->setHelpfulVote($this->getHelpfulVote() + 1);
            if (!$isNew) {
                $this->setUnhelpfulVote($this->getUnhelpfulVote() - 1);
            }
        } else {
            $this->setUnhelpfulVote($this->getUnhelpfulVote() + 1);
            if (!$isNew) {
                $this->setHelpfulVote($this->getHelpfulVote() - 1);
            }
        }
        return $this;
    }

    /**
     * Unvote
     *
     * @param int $type
     */
    public function unvote($type)
    {
        if ($type < 0) {
            $this->setHelpfulVote($this->getHelpfulVote() - 1);
        } else {
            $this->setUnhelpfulVote($this->getUnhelpfulVote() - 1);
        }
        return $this;
    }

    /**
     * Get vote data
     *
     * @return mixed
     */
    public function getVoteData()
    {
        $result['like'] = 0;
        $result['unlike'] = 0;
        $result['state'] = null;
        $customerId = $this->moduleHelper->getCustomerSession()->getCustomerId();
        $sessionVoteId = $this->moduleHelper->getCustomerSession()->getData('bss_faq_vote_' . $this->getId());
        $voteCollection = $this->voteCollectionFactory->create();
        $result = $voteCollection->addFieldToFilter('faq_id', $this->getId())->getVoteData($customerId, $sessionVoteId);
        if (!$this->getUseRealVoteData()) {
            $result['like'] = $this->getHelpfulVote();
            $result['unlike'] = $this->getUnhelpfulVote();
            if ($customerId && isset($result['state'])) {
                if ($result['state'] == FaqsVote::VOTE_STATE_UNLIKE) {
                    $result['unlike']++;
                } else {
                    $result['like']++;
                }
            } else {
                $vote = $this->faqVoteFactory->create()->load($sessionVoteId);
                if (!$vote->getId()) {
                    return $result;
                }
                if ($vote->getVoteValue() < 0) {
                    $result['unlike']++;
                    $result['state'] = FaqsVote::VOTE_STATE_UNLIKE;
                } else {
                    $result['like']++;
                    $result['state'] = FaqsVote::VOTE_STATE_LIKE;
                }
            }
        }
        return $result;
    }

    /**
     * Get store name
     *
     * @return array
     */
    public function getStoreName()
    {
        $this->afterLoadStore();
        $stores = $this->moduleHelper->getStores();
        $storeIds = $this->getStoreArray();
        $result = [];
        foreach ($storeIds as $storeId) {
            if (isset($stores[$storeId])) {
                $result[] = $stores[$storeId]->getName();
            }
        }
        return $result;
    }

    /**
     * Get store array
     *
     * @return array
     */
    public function getStoreArray()
    {
        if (is_array($this->getStoreId())) {
            return $this->getStoreId();
        } else {
            return $this->getStoreId() ? explode(',', $this->getStoreId()) : [];
        }
    }

    /**
     * Get link collection
     *
     * @param array $ids
     * @return mixed
     */
    public function getLinkCollection($ids = [])
    {
        if (empty($ids)) {
            $ids = $this->getLinkId();
        }
        return $this->categoryCollectionFactory->create()->addIdFilter($ids);
    }

    /**
     * Get link id
     *
     * @return array
     */
    public function getLinkId()
    {
        return $this->getCategoryId() ? explode(';', $this->getCategoryId()) : [];
    }

    /**
     * Set link id
     *
     * @param array $ids
     * @return mixed
     */
    public function setLinkId($ids)
    {
        return $this->setCategoryId(implode(';', $ids));
    }

    /**
     * Get link id oldvalue
     *
     * @return array
     */
    public function getLinkIdOldvalue()
    {
        return $this->getCategoryIdOldvalue() ? explode(';', $this->getCategoryIdOldvalue()) : [];
    }

    /**
     * Get tag array
     *
     * @return array
     */
    public function getTagArray()
    {
        return $this->getTag() ? explode(',', $this->getTag()) : [];
    }

    /**
     * Get product id array
     *
     * @return array
     */
    public function getProductIdArray()
    {
        return $this->getProductId() ? explode(';', $this->getProductId()) : [];
    }
}
