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

class FaqCategory extends AbstractModel
{
    const IMAGE_FIELD_NAME = 'cate_uploader';
    const IMAGE_PATH = 'bss/FaqCategory/';

    /**
     * @var string
     */
    protected $_eventPrefix = 'faqs_category';

    /**
     * @var \Bss\Faqs\Model\ResourceModel\Faqs\CollectionFactory
     */
    private $faqCollectionFactory;

    /**
     * @var \Bss\Faqs\Helper\ModuleConfig
     */
    private $moduleHelper;

    /**
     * FaqCategory constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param ResourceModel\Faqs\CollectionFactory $faqCollectionFactory
     * @param \Bss\Faqs\Helper\ModuleConfig $moduleHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filter\FilterManager $filter,
        \Bss\Faqs\Model\ResourceModel\Faqs\CollectionFactory $faqCollectionFactory,
        \Bss\Faqs\Helper\ModuleConfig $moduleHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $filter,
            $resource,
            $resourceCollection,
            $data
        );
        $this->faqCollectionFactory = $faqCollectionFactory;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Bss\Faqs\Model\ResourceModel\FaqCategory::class);
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

        if ($this->getData('del_image')) {
            $this->setImage('');
        } else {
            try {
                $image = $this->moduleHelper->saveImage(self::IMAGE_FIELD_NAME, self::IMAGE_PATH);
            } catch (\Exception $e) {
                $image = $this->getOldImage();
            }
            $this->setImage($image);
        }
    }

    /**
     * Get view data
     *
     * @param bool $hasLink
     * @return mixed
     */
    public function getViewData($hasLink = true)
    {
        if ($hasLink) {
            $storeId = $this->moduleHelper->getStoreId();
            $linkCollection = $this->faqCollectionFactory->create();
            if ($this->getFaqSort()) {
                $linkCollection->addFaqSort($this->getFaqSort());
            }
            $linkCollection
            ->addIdFilter($this->getLinkId())
            ->addStoreFilter($storeId);
            if ($this->getLimitLink()) {
                $linkCollection->addFaqLimit($this->getLimitLink());
            }
            $this->setFaq($linkCollection->getViewData(false));
        }
        return $this->getData();
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
        return $this->faqCollectionFactory->create()->addIdFilter($ids);
    }

    /**
     * Get link id
     *
     * @return array
     */
    public function getLinkId()
    {
        return $this->getFaqId() ? explode(';', $this->getFaqId()) : [];
    }

    /**
     * Set link id
     *
     * @param array $ids
     * @return mixed
     */
    public function setLinkId($ids)
    {
        return $this->setFaqId(implode(';', $ids));
    }

    /**
     * Get link id oldvalue
     *
     * @return array
     */
    public function getLinkIdOldvalue()
    {
        return $this->getFaqIdOldvalue() ? explode(';', $this->getFaqIdOldvalue()) : [];
    }
}
