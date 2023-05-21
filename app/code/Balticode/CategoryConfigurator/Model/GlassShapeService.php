<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Balticode\CategoryConfigurator\Helper\UnitConversionHelper;
use Balticode\CategoryConfigurator\Model\Validator\StepsData;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GlassShapeService
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param StoreManagerInterface $storeManager
     * @param GeneralConfig $generalConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        GeneralConfig $generalConfig,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->generalConfig = $generalConfig;
        $this->logger = $logger;
    }

    /**
     * @param array $glassShape
     * @return float|int
     */
    public function calculateQty($glassShape)
    {
        if (!isset($glassShape[StepsData::ARRAY_INDEX_HEIGHT])) {
            return 0;
        }

        $width = UnitConversionHelper::convertMillimetersToMeters($this->determineWidth($glassShape));
        $height = UnitConversionHelper::convertMillimetersToMeters($glassShape[StepsData::ARRAY_INDEX_HEIGHT]);

        return $width * $height;
    }

    /**
     * @param array $stepProduct
     * @param DataObject $glassShapeOption
     * @return array|null
     */
    public function getResolvedDimensionOptionValue($stepProduct, $glassShapeOption)
    {
        $storeId = $this->getCurrentStoreId();

        if (!isset($stepProduct[StepsData::ARRAY_INDEX_USES_CUSTOM_WIDTH]) || !$storeId) {
            return null;
        }

        if (!$glassShapeOption->getData('is_require') && !$stepProduct[StepsData::ARRAY_INDEX_USES_CUSTOM_WIDTH]) {
            return null;
        }

        $value = 0;

        switch ($glassShapeOption->getData('title')) {
            case $this->generalConfig->getGlassShapesHeightTitle($storeId):
                if (isset($stepProduct[StepsData::ARRAY_INDEX_HEIGHT])) {
                    $value = $stepProduct[StepsData::ARRAY_INDEX_HEIGHT];
                }

                break;
            case $this->generalConfig->getGlassShapesWidthTitle($storeId):
                $value = $this->determineWidth($stepProduct);

                break;
            case $this->generalConfig->getGlassShapesTopWidthTitle($storeId):
                if (isset($stepProduct[StepsData::ARRAY_INDEX_TOP_WIDTH])) {
                    $value = $stepProduct[StepsData::ARRAY_INDEX_TOP_WIDTH];
                }

                break;
            case $this->generalConfig->getGlassShapesBottomWidthTitle($storeId):
                if (isset($stepProduct[StepsData::ARRAY_INDEX_BOTTOM_WIDTH])) {
                    $value = $stepProduct[StepsData::ARRAY_INDEX_BOTTOM_WIDTH];
                }

                break;
            default:
                return null;
        }

        return [
            'id' => $glassShapeOption->getData('option_id'),
            'value' => $value
        ];
    }

    /**
     * @param array $stepData
     * @return int
     */
    protected function determineWidth($stepData)
    {
        if (!isset($stepData[StepsData::ARRAY_INDEX_USES_CUSTOM_WIDTH]) ||
            !isset($stepData[StepsData::ARRAY_INDEX_WIDTH])
        ) {
            return 0;
        }

        if (!$stepData[StepsData::ARRAY_INDEX_USES_CUSTOM_WIDTH]) {
            return $stepData[StepsData::ARRAY_INDEX_WIDTH];
        }

        if (!isset($stepData[StepsData::ARRAY_INDEX_TOP_WIDTH]) ||
            !isset($stepData[StepsData::ARRAY_INDEX_BOTTOM_WIDTH])
        ) {
            return 0;
        }

        $topWidth = $stepData[StepsData::ARRAY_INDEX_TOP_WIDTH];
        $bottomWidth = $stepData[StepsData::ARRAY_INDEX_BOTTOM_WIDTH];

        if ($topWidth > $bottomWidth) {
            return $topWidth;
        }

        return $bottomWidth;
    }

    /**
     * @return int
     */
    protected function getCurrentStoreId()
    {
        try {
            $store = $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());

            return 0;
        }

        if (!$store instanceof StoreInterface) {
            return 0;
        }

        return $store->getId();
    }
}