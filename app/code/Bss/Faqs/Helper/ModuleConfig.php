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
namespace Bss\Faqs\Helper;

use Magento\Framework\Exception\NoSuchEntityException;

class ModuleConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var int
     */
    protected $storeId = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * ModuleConfig constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploader
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->date = $date;
        $this->filterProvider = $filterProvider;
        $this->customerSession = $customerSession;
    }

    /**
     * Get stores
     *
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->storeManager->getStores();
    }

    /**
     * Get customer session
     *
     * @return mixed
     */
    public function getCustomerSession()
    {
        return $this->customerSession->create();
    }

    /**
     * Get store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if (!isset($this->storeId)) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * Converts input date into date with timezone offset
     *
     * @param  string $format
     * @param  int|string $input date in GMT timezone
     * @return string
     */
    public function date($format = null, $input = null)
    {
        return $this->date->date($format, $input);
    }

    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function pageFilter($value)
    {
        return $this->filterProvider->getPageFilter()->filter($value);
    }

    /**
     * Save image
     *
     * @param string $field
     * @param string $path
     * @return string
     */
    public function saveImage($field, $path)
    {
        $uploader = $this->uploader->create(['fileId' => $field]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'bmp']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $mediaDirectory = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
        $fileResult = $uploader->save($mediaDirectory->getAbsolutePath($path));

        $result = $this->storeManager->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($path, $fileResult['file']);
        return $result;
    }

    /**
     * Get file path
     *
     * @param string $path
     * @param string $imageName
     * @return string
     */
    private function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Is module enable
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isModuleEnable($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/Bss_Faqs/Enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is add roboto
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isAddRoboto($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/Bss_Faqs/add_roboto',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is add awesome
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isAddAwesome($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/Bss_Faqs/add_awesome',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get base url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Is show most faq
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isShowMostFaq($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/most_faq_show',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Most faq number
     *
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function mostFaqNumber($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/most_faq_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is show related faq
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isShowRelatedFaq($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/related_faq_show',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Question per category
     *
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function questionPerCategory($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/question_per_category',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Question display
     *
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function questionDisplay($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/question_display',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get background color
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBackgroundColor($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/background_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sort by
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSortBy($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/faq_sort_by',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is Require Login Voting
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function isRequireLoginVoting($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/require_login_vote',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Show social button
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function showSocialButton($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/social_button',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Full question product page
     *
     * @param int $storeId
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function fullQuestionProductPage($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/display/full_question_product_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get message search result
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMessageSearchResult($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/message/search',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get message tag result
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMessageTagResult($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/message/tag',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get message helpful
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMessageHelpful($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/message/helpful',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get message unhelpful
     *
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMessageUnhelpful($storeId = null)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            'faqs_config/message/unhelpful',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
