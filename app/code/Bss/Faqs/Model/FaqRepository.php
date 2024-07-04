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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class FaqRepository extends AbstractModel
{
    /**
     * @var FaqsFactory
     */
    protected $faqFactory;

    /**
     * @var FaqCategory
     */
    protected $categoryFactory;

    /**
     * @var FaqsVoteFactory
     */
    protected $faqVoteFactory;

    /**
     * @var \Bss\Faqs\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var ResourceModel\FaqsVote\CollectionFactory
     */
    protected $voteCollectionFactory;

    /**
     * @param \Bss\Faqs\Helper\ModuleConfig $moduleConfig
     * @param FaqsFactory $faqFactory
     * @param FaqCategoryFactory $categoryFactory
     * @param FaqsVoteFactory $faqVoteFactory
     * @param ResourceModel\FaqsVote\CollectionFactory $voteCollectionFactory
     */
    public function __construct(
        \Bss\Faqs\Helper\ModuleConfig $moduleConfig,
        FaqsFactory $faqFactory,
        FaqCategoryFactory $categoryFactory,
        FaqsVoteFactory $faqVoteFactory,
        \Bss\Faqs\Model\ResourceModel\FaqsVote\CollectionFactory $voteCollectionFactory
    ) {
        $this->faqFactory = $faqFactory;
        $this->faqVoteFactory = $faqVoteFactory;
        $this->categoryFactory = $categoryFactory;
        $this->moduleConfig = $moduleConfig;
        $this->voteCollectionFactory = $voteCollectionFactory;
    }

    /**
     * Get faq by id
     *
     * @param int $id
     * @param int $storeId
     * @return Faqs|null
     */

    public function getFaqById($id, $storeId = null)
    {
        $this->validateModuleEnable();
        if ($storeId == null) {
            $storeId = $this->moduleConfig->getStoreId();
        }
        $faq = $this->faqFactory->create()->load($id);
        if ($faq->getId() && array_search($storeId, $faq->getStoreId()) !== false) {
            $this->moduleConfig->getCustomerSession()->setCurrentFaqView($faq->getId());
            return $faq->getViewData();
        } else {
            throw new NoSuchEntityException(__('Requested FAQ doesn\'t exist'));
        }
    }

    /**
     * Get faq by url
     *
     * @param string $url
     * @param int $storeId
     * @return Faqs|null
     */

    public function getFaqByUrl($url, $storeId = null)
    {
        $this->validateModuleEnable();
        if ($storeId == null) {
            $storeId = $this->moduleConfig->getStoreId();
        }
        $faq = $this->faqFactory->create()->getCollection()
        ->addStoreFilter($storeId)
        ->getItemByColumnValue('url_key', $url);
        if ($faq) {
            $this->moduleConfig->getCustomerSession()->setCurrentFaqView($faq->getId());
            return $faq->getViewData(true, $storeId);
        } else {
            throw new NoSuchEntityException(__('Requested FAQ doesn\'t exist'));
        }
    }

    /**
     * Get faq by tag
     *
     * @param array $tagList
     * @param string $sortBy
     * @return array
     */
    public function getFaqByTag($tagList, $sortBy)
    {
        $this->validateModuleEnable();
        $collection = $this->faqFactory->create()->getCollection()
        ->addFaqSort($sortBy)
        ->addEmptyFilter()
        ->addStoreFilter($this->moduleConfig->getStoreId())
        ->addTagFilter($tagList);

        $searchResult = $collection->getViewData(false);
        $result = [];
        $counter = count($searchResult);
        $result['result'] = $searchResult;
        $result['message'] = strtr(
            __($this->moduleConfig->getMessageTagResult()),
            ['$tag$' => '<span>' . implode('~', $tagList) . '</span>', '$counter$' => $counter]
        );
        return $result;
    }

    /**
     * Get category by url
     *
     * @param string $url
     * @param string $sort
     * @return mixed|null
     */
    public function getCategoryByUrl($url, $sort = 'time')
    {
        $this->validateModuleEnable();
        $category = $this->categoryFactory->create()->getCollection()->getItemByColumnValue('url_key', $url);
        if ($category) {
            return $category->setFaqSort($sort)->getViewData(true);
        } else {
            throw new NoSuchEntityException(__('Requested FAQ Category doesn\'t exist'));
        }
    }

    /**
     * Get category by id
     *
     * @param int $id
     * @param string $sort
     * @return mixed|null
     */
    public function getCategoryById($id, $sort = 'time')
    {
        $this->validateModuleEnable();
        $category = $this->categoryFactory->create()->load($id);
        if ($category->getId()) {
            return $category->setFaqSort($sort)->getViewData(true);
        } else {
            throw new NoSuchEntityException(__('Requested FAQ Category doesn\'t exist'));
        }
    }

    /**
     * Get category image
     *
     * @param int $cateId
     * @return array
     */
    public function getCategoryImage($cateId)
    {
        $this->validateModuleEnable();
        if ($cateId != null) {
            return $this->getCategoryById($cateId);
        }
        return $this->categoryFactory->create()->setImage('')->getData();
    }

    /**
     * Get all data
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getAllData()
    {
        $this->validateModuleEnable();
        $questionPerCategory = $this->moduleConfig->questionPerCategory();
        $defaultSort = $this->moduleConfig->getSortBy();
        return $this->categoryFactory->create()->getCollection()
        ->addFieldToFilter('show_in_mainpage', 1)
        ->setLimitLink($questionPerCategory)
        ->addEmptyFilter()
        ->addVisibleFilter()
        ->setFaqSort($defaultSort)
        ->getViewData(true);
    }

    /**
     * Get sidebar data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getSideBarData()
    {
        $this->validateModuleEnable();
        $result = [];
        $mostFaqNumber = $this->moduleConfig->mostFaqNumber();
        $storeId = $this->moduleConfig->getStoreId();
        $faqCollection = $this->faqFactory->create()->getCollection()->addStoreFilter($storeId);
        $result['tag'] = $faqCollection->getAllTag();
        $result['most_faq'] = $faqCollection->getMostFaqData($mostFaqNumber);
        $result['category'] = $this->categoryFactory->create()->getCollection()
        ->addEmptyFilter()->getViewData(false);
        return $result;
    }

    /**
     * Get product faq data
     *
     * @param int $id
     * @return array
     */
    public function getProductFaqData($id)
    {
        $this->validateModuleEnable();
        $storeId = $this->moduleConfig->getStoreId();
        $result = [];
        $faqsCollection = $this->faqFactory->create()->getCollection()
        ->addEmptyFilter()
        ->addStoreFilter($storeId)
        ->addProductFilter($id);
        $result['user'] = $this->moduleConfig->getCustomerSession()->getCustomer()->getEmail();
        $result['faq'] = $faqsCollection->getViewData(false);
        return $result;
    }

    /**
     * Submit new question
     *
     * @param string $question
     * @param int $productId
     * @param string $customer
     * @return \Magento\Framework\Phrase
     * @throws LocalizedException
     */
    public function submitNewQuestion($question, $productId, $customer)
    {
        $this->validateModuleEnable();
        $data = [
            'title' => $question,
            'product_id' => $productId,
            'customer' => $customer,
            'answer' => __('This question is not answered!')
        ];
        $this->faqFactory->create()->addData($data)->save();
        return __('Thanks for your submit');
    }

    /**
     * Get faq voting
     *
     * @param int $faqId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getFaqVoting($faqId)
    {
        $faq = $this->faqFactory->create()->load($faqId);
        if ($faq->getId()) {
            return $faq->getVoteData();
        } else {
            throw new NoSuchEntityException(__('Requested FAQ doesn\'t exist'));
        }
    }

    /**
     * Update vote
     *
     * @param string $type
     * @param int $faqId
     * @param int $customerId
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateVote($type, $faqId, $customerId = null)
    {
        $this->validateModuleEnable();
        if ($customerId === null){
            $customerId = $this->moduleConfig->getCustomerSession()->getCustomerId();
        }
        $faq = $this->faqFactory->create()->load($faqId);
        if (!$faq->getId()) {
            throw new NoSuchEntityException(__('Requested FAQ doesn\'t exist'));
        }
        if (!$customerId) {
            if ($this->moduleConfig->isRequireLoginVoting()) {
                throw new LocalizedException(__('You need login to vote'));
            } else {
                return $this->notLoggedinVote($type, $faq);
            }
        } else {
            return $this->loggedinVote($type, $faq, $customerId);
        }
    }

    /**
     * Get anonymous vote
     *
     * @param int $faqId
     * @return mixed
     */
    private function getAnonymousVote($faqId)
    {
        $anonVote = $this->faqVoteFactory->create()->getCollection()
        ->addFieldToFilter('faq_id', $faqId)->getAnonymousVote();
        if (!isset($anonVote[FaqsVote::ANONYMOUS_LIKE])) {
            $anonVote[FaqsVote::ANONYMOUS_LIKE] = $this->faqVoteFactory->create()
            ->setFaqId($faqId)->setUserId(FaqsVote::ANONYMOUS_LIKE);
        }
        if (!isset($anonVote[FaqsVote::ANONYMOUS_UNLIKE])) {
            $anonVote[FaqsVote::ANONYMOUS_UNLIKE] = $this->faqVoteFactory->create()
            ->setFaqId($faqId)->setUserId(FaqsVote::ANONYMOUS_UNLIKE);
        }
        return $anonVote;
    }

    /**
     * Not loggedin vote
     *
     * @param string $type
     * @param mixed $faq
     * @return mixed
     */
    public function notLoggedinVote($type, $faq)
    {
        $voteRow = $this->faqVoteFactory->create();
        if ($type > 0) {
            $userId = FaqsVote::ANONYMOUS_LIKE;
        } else {
            $userId = FaqsVote::ANONYMOUS_UNLIKE;
        }
        $faqId = $faq->getId();
        if ($sessionVoteId = $this->moduleConfig->getCustomerSession()->getData('bss_faq_vote_' . $faqId)) {
            $voteRow->load($sessionVoteId);
            if ($voteRow->getVoteValue() != $type) {
                $voteRow->setUserId($userId)->setVoteValue($type)->save();
            } else {
                $voteRow->delete();
                $this->moduleConfig->getCustomerSession()->setData('bss_faq_vote_' . $faqId, null);
            }

        } else {
            $voteRow->setFaqId($faqId)->setUserId($userId)->setVoteValue($type)->save();
            $this->moduleConfig->getCustomerSession()->setData('bss_faq_vote_' . $faqId, $voteRow->getId());
        }
        $message = $this->getVoteMessage($type);
        return $message;
    }

    /**
     * Loggedin vote
     *
     * @param string $type
     * @param mixed $faq
     * @param int $customerId
     * @return mixed
     */
    public function loggedinVote($type, $faq, $customerId)
    {
        $voteCollection = $this->voteCollectionFactory->create()
            ->addFieldToFilter('faq_id', $faq->getId())
            ->addFieldToFilter('user_id', $customerId);
        if ($voteCollection->getSize() == 0) {
            $voteRow = $this->faqVoteFactory->create();
            $voteRow->setFaqId($faq->getId())->setUserId($customerId)->setVoteValue($type)->save();
        } else {
            foreach ($voteCollection as $vote) {
                if ($vote->getVoteValue() != $type) {
                    $vote->setVoteValue($type)->save();
                } else {
                    $vote->delete();
                }
            }
        }
        $message = $this->getVoteMessage($type);
        return $message;
    }

    /**
     * Get vote message
     *
     * @param string $type
     * @return mixed
     */
    public function getVoteMessage($type)
    {
        $message = [
            '0' => null,
            '1' => $this->moduleConfig->getMessageHelpful(),
            '-1' => $this->moduleConfig->getMessageUnhelpful()
        ];
        return $message[$type];
    }

    /**
     * Get search data
     *
     * @param string $keyword
     * @param int $categoryId
     * @param string $sortBy
     * @param bool $searchInTitle
     * @return array
     */
    public function getSearchData($keyword, $categoryId, $sortBy, $searchInTitle = true)
    {
        $this->validateModuleEnable();
        $storeId = $this->moduleConfig->getStoreId();
        $faqCollection = $this->faqFactory->create()->getCollection()
        ->faqSearch($keyword, $searchInTitle)
        ->addFaqSort($sortBy)
        ->addEmptyFilter()
        ->addStoreFilter($storeId);
        if ($categoryId) {
            $category = $this->categoryFactory->create()->load($categoryId);
            if ($category->getId()) {
                $faqCollection->addIdFilter($category->getLinkId());
            } else {
                throw new NoSuchEntityException(__('Requested FAQ Category doesn\'t exist'));
            }
        }
        $searchResult = $faqCollection->getViewData(false);
        $result = [];
        $counter = count($searchResult);
        $result['result'] = $searchResult;
        $result['message'] = strtr(
            __($this->moduleConfig->getMessageSearchResult()),
            ['$key$' => $keyword, '$counter$' => $counter]
        );
        return $result;
    }

    /**
     * Validate module enable
     *
     * @throws LocalizedException
     */
    public function validateModuleEnable()
    {
        if (!$this->moduleConfig->isModuleEnable()) {
            throw new LocalizedException(__('FAQs is Disabled'));
        }
    }
}
