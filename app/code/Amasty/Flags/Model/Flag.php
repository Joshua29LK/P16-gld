<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */

namespace Amasty\Flags\Model;

use Amasty\Flags\Api\Data\FlagInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

class Flag extends AbstractModel implements FlagInterface
{
    const FLAGS_FOLDER = 'ordergridicons/flags';
    const IMAGE_PLACEHOLDER = 'Amasty_Flags::img/empty.png';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
        $this->assetRepository = $assetRepository;
    }

    protected function _construct()
    {
        $this->_init('Amasty\Flags\Model\ResourceModel\Flag');
    }

    /**
     * @param Document|Flag|null $flag
     *
     * @return string
     */
    public function getImageUrl($flag = null)
    {
        $url = $this->getUploadUrl() . ($flag ? $flag->getImageName() : $this->getImageName());
        return $url;
    }

    public function getUploadUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        return $baseUrl . self::FLAGS_FOLDER . '/';
    }

    public function getImageRelativePath()
    {
        return self::FLAGS_FOLDER . '/' . $this->getImageName();
    }

    public function getImagePlaceholderUrl()
    {
        return $this->assetRepository->createAsset(self::IMAGE_PLACEHOLDER)->getUrl();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(FlagInterface::NAME);
    }

    /**
     * @param string $name
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setName($name)
    {
        return $this->setData(FlagInterface::NAME, $name);
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->getData(FlagInterface::IMAGE_NAME);
    }

    /**
     * @param string $imageName
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setImageName($imageName)
    {
        return $this->setData(FlagInterface::IMAGE_NAME, $imageName);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getData(FlagInterface::PRIORITY);
    }

    /**
     * @param int $priority
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setPriority($priority)
    {
        return $this->setData(FlagInterface::PRIORITY, $priority);
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getData(FlagInterface::NOTE);
    }

    /**
     * @param string $note
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setNote($note)
    {
        return $this->setData(FlagInterface::NOTE, $note);
    }

    /**
     * @return int|null
     */
    public function getApplyColumn()
    {
        return $this->getData(FlagInterface::APPLY_COLUMN);
    }

    /**
     * @param int|null $applyColumn
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyColumn($applyColumn)
    {
        return $this->setData(FlagInterface::APPLY_COLUMN, $applyColumn);
    }

    /**
     * @return string
     */
    public function getApplyStatus()
    {
        return $this->getData(FlagInterface::APPLY_STATUS);
    }

    /**
     * @param string $applyStatus
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyStatus($applyStatus)
    {
        return $this->setData(FlagInterface::APPLY_STATUS, $applyStatus);
    }

    /**
     * @return string
     */
    public function getApplyShipping()
    {
        return $this->getData(FlagInterface::APPLY_SHIPPING);
    }

    /**
     * @param string $applyShipping
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyShipping($applyShipping)
    {
        return $this->setData(FlagInterface::APPLY_SHIPPING, $applyShipping);
    }

    /**
     * @return string
     */
    public function getApplyPayment()
    {
        return $this->getData(FlagInterface::APPLY_PAYMENT);
    }

    /**
     * @param string $applyPayment
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyPayment($applyPayment)
    {
        return $this->setData(FlagInterface::APPLY_PAYMENT, $applyPayment);
    }
}
