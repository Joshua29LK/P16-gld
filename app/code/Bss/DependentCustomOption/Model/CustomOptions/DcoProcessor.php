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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\DependentCustomOption\Model\CustomOptions;

use Bss\DependentCustomOption\Api\Data\DependentOptionInterface;
use Bss\DependentCustomOption\Api\Data\DependentOptionInterfaceFactory;
use Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterface;
use Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterfaceFactory;
use Bss\DependentCustomOption\Model\DependOptionFactory;
use Bss\DependentCustomOption\Model\ResourceModel\DependOption as DcoResource;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Catalog\Model\Product\Option\ValueFactory;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Option as OptionResource;
use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value as ValueResource;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class DcoProcessor
{
    /**
     * Const
     */
    const KEY_DEPEND_VALUE = 'depend_value';
    const KEY_DEPENDENT_ID = 'dependent_id';
    const KEY_INCREMENT_ID = 'increment_id';
    const KEY_SYMBOL_SEPARATE = '>';
    const KEY_SYMBOL_SEPARATE_VALUE = '&';

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var OptionResource
     */
    protected $optionResource;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * @var OptionRepository
     */
    protected $customOptionRepository;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var ProductCustomOptionInterfaceFactory
     */
    protected $customOptionFactory;

    /**
     * @var ProductCustomOptionValuesInterfaceFactory
     */
    protected $productCustomOptionValuesFactory;

    /**
     * @var DependentOptionInterfaceFactory
     */
    protected $dependentOptionTemplate;

    /**
     * @var DependentOptionValuesInterfaceFactory
     */
    protected $dependentOptionValuesTemplate;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var DcoResource
     */
    protected $dcoResource;

    /**
     * @var ValueFactory
     */
    protected $valueFactory;

    /**
     * @var ValueResource
     */
    protected $valueResource;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $productOptions = [];

    /**
     * DcoProcessor constructor.
     * @param OptionFactory $optionFactory
     * @param OptionResource $optionResource
     * @param ProductRepositoryInterface $productRepository
     * @param DependOptionFactory $dependOptionFactory
     * @param OptionRepository $customOptionRepository
     * @param ProductCustomOptionInterfaceFactory $customOptionFactory
     * @param ProductCustomOptionValuesInterfaceFactory $productCustomOptionValuesFactory
     * @param DependentOptionInterfaceFactory $dependentOptionFactory
     * @param DependentOptionValuesInterfaceFactory $dependentOptionValuesTemplate
     * @param Converter $converter
     * @param DcoResource $dcoResource
     * @param ValueFactory $valueFactory
     * @param ValueResource $valueResource
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        OptionFactory $optionFactory,
        OptionResource $optionResource,
        ProductRepositoryInterface $productRepository,
        DependOptionFactory $dependOptionFactory,
        OptionRepository $customOptionRepository,
        ProductCustomOptionInterfaceFactory $customOptionFactory,
        ProductCustomOptionValuesInterfaceFactory $productCustomOptionValuesFactory,
        DependentOptionInterfaceFactory $dependentOptionFactory,
        DependentOptionValuesInterfaceFactory $dependentOptionValuesTemplate,
        Converter $converter,
        DcoResource $dcoResource,
        ValueFactory $valueFactory,
        ValueResource $valueResource,
        CollectionFactory $collectionFactory
    ) {
        $this->optionFactory = $optionFactory;
        $this->optionResource = $optionResource;
        $this->productRepository = $productRepository;
        $this->dependOptionFactory = $dependOptionFactory;
        $this->customOptionRepository = $customOptionRepository;
        $this->customOptionFactory = $customOptionFactory;
        $this->productCustomOptionValuesFactory = $productCustomOptionValuesFactory;
        $this->dependentOptionTemplate = $dependentOptionFactory;
        $this->dependentOptionValuesTemplate = $dependentOptionValuesTemplate;
        $this->converter = $converter;
        $this->dcoResource = $dcoResource;
        $this->valueFactory = $valueFactory;
        $this->valueResource = $valueResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param DataObject|DependentOptionInterface $dependentOption
     * @return int|null
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws StateException
     */
    public function saveOption($dependentOption)
    {
        $productSku = $dependentOption->getProductSku();
        if (!$productSku) {
            throw new CouldNotSaveException(__('The ProductSku is empty. Set the ProductSku and try again.'));
        }
        $product = $this->productRepository->get($dependentOption->getProductSku());
        $productId = $product->getId();
        if ($dependentOption->getOptionId()) {
            $defaultOption = $this->customOptionRepository->get($product->getSku(), $dependentOption->getOptionId());
        } else {
            $defaultOption = $this->customOptionFactory->create();
        }

        $this->converter->convertOption($dependentOption, $defaultOption);

        $optionValues = [];
        $dcoOptionValues = $dependentOption->getValues();
        // Process save new values
        /** @var DependentOptionValuesInterface $dependentOptionValue */
        foreach ($dcoOptionValues as $dependentOptionValue) {
            $optionValue = $this->productCustomOptionValuesFactory->create();
            $this->converter->convertOptionValues($dependentOptionValue, $optionValue);
            $optionValues[] = $optionValue;
        }
        $defaultOption->setValues($optionValues);
        $customOption = $this->customOptionRepository->save($defaultOption);
        if (!$dependentOption->getOptionId()) {
            $product->addOption($customOption);
            $this->productRepository->save($product);
        }

        // Get all options
        $allOptions = $this->dcoResource->getAllOptionIdsByProduct($productId);
        $boilerplateList = [];
        $boilerplateOptionList = [];

        // Remain old dco values
        $oldDcoList = $this->dcoResource->getDcoByProduct($productId);
        // Remove old dco of product
        $this->dcoResource->removeOldDco($productId);

        $lastIncrementProduct = $this->getLastIncrementId($productId);
        // Get options dco data to insert
        foreach ($allOptions as $option) {
            if (!isset($boilerplateOptionList[$option['option_id']])) {
                $boilerplateOption = $this->createBoilerplateDco(
                    $lastIncrementProduct,
                    $productId,
                    $option['option_id'],
                    NULL,
                    1
                );
                $boilerplateOptionList[$option['option_id']] = $boilerplateOption;
            }
        }
        // Get option values dco data to insert
        foreach ($allOptions as $option) {
            if (isset($option['option_type_id']) && $option['option_type_id']) {
                $boilerplateOption = $this->createBoilerplateDco(
                    $lastIncrementProduct,
                    $productId,
                    $option['option_type_id'],
                    NULL,
                    2
                );
                $boilerplateList[$option['option_id']][] = $boilerplateOption;
            }
        }

        // Merge boilerplate to valid sort order
        $pushArr = [];
        foreach ($allOptions as $option) {
            if (!in_array($option['option_id'], $pushArr)) {
                $boilerplateList[$option['option_id']][] = $boilerplateOptionList[$option['option_id']];
                $pushArr[] = $option['option_id'];
            }
        }
        // Get all list data will be inserted
        $list = [];
        foreach ($boilerplateList as $boilerplate) {
            foreach ($boilerplate as $item) {
                $list[] = $item;
            }
        }

        // Save new dco after remove old dco
        foreach ($oldDcoList as $oldDcoItem) {
            if (!isset($oldDcoItem['option_type_id']) && !isset($oldDcoItem['option_id']) &&
                !$oldDcoItem['option_type_id'] && !$oldDcoItem['option_id']) {
                continue;
            }
            foreach ($list as &$item) {
                if (isset($item['option_id']) && isset($oldDcoItem['option_id']) &&
                    $item['option_id'] && $oldDcoItem['option_id'] &&
                    $item['option_id'] == $oldDcoItem['option_id'] &&
                    isset($oldDcoItem['depend_value']) && $oldDcoItem['depend_value']) {
                    $item['depend_value'] = $oldDcoItem['depend_value'];
                }
                if (isset($item['option_type_id']) && isset($oldDcoItem['option_type_id']) &&
                    $item['option_type_id'] && $oldDcoItem['option_type_id'] &&
                    $item['option_type_id'] == $oldDcoItem['option_type_id'] &&
                    isset($oldDcoItem['depend_value']) && $oldDcoItem['depend_value']) {
                    $item['depend_value'] = $oldDcoItem['depend_value'];
                }
            }
        }
        $this->dcoResource->saveNewDcos($list);

        // Process update dco with depend value
        /** @var DependentOptionValuesInterface $dcoOptionValue */
        foreach ($dcoOptionValues as $dcoOptionValue) {
            if (!$dcoOptionValue->getDependValue()) {
                continue;
            }

            $dependOnValue = null;
            $dependOnValuesStr = explode(self::KEY_SYMBOL_SEPARATE_VALUE, $dcoOptionValue->getDependValue());
            foreach ($dependOnValuesStr as $dependOnValueStr) {
                // format depend OptionTitle > OptionType > OptionValueTitle
                $dependOnValueArr = explode(self::KEY_SYMBOL_SEPARATE, $dependOnValueStr);
                if (count($dependOnValueArr) != 2 && count($dependOnValueArr) != 3) {
                    continue;
                }
                $optionId = $this->getExactOptionId($dependOnValueArr, $product->getId());
                $optionTypeId = $this->getExactOptionTypeId($dependOnValueArr, $optionId);
                if ($optionTypeId) {
                    $loadedDco = $this->dcoResource->loadByOptionTyeId($optionTypeId);
                } else {
                    $loadedDco = $this->dcoResource->loadByOptionId($optionId);
                }
                // if options already exist
                if ($loadedDco) {
                    $dependOnValue = $dependOnValue . ',' . $loadedDco['increment_id'];
                }
            }
            if ($dependOnValue) {
                $updateDcoOnOptionTypeId = $dcoOptionValue->getOptionTypeId();
                if ($updateDcoOnOptionTypeId) {
                    // If update option type id
                    $this->dcoResource->updateDependValue($productId, $dcoOptionValue->getOptionTypeId(), trim($dependOnValue, ','));
                } else {
                    // If add new option type id
                    $currentOptionTypeTitle = $dcoOptionValue->getTitle();
                    $optionTypeMustUpdate = $this->getExactOptionTypeId($currentOptionTypeTitle, $customOption->getOptionId());
                    $this->dcoResource->updateDependValue($productId, $optionTypeMustUpdate, trim($dependOnValue, ','));
                }
            }
        }
        return $customOption->getOptionId();
    }

    /**
     * @param int $productId
     * @return int|string
     * @throws LocalizedException
     */
    protected function getLastIncrementId($productId)
    {
        $lastIncrementProduct = $this->dcoResource->getLastIncrementId($productId);
        if (!$lastIncrementProduct) {
            return 1;
        }
        $lastIncrementProduct++;
        return $lastIncrementProduct;
    }

    /**
     * @param $incrementId
     * @param $productId
     * @param $optionId
     * @param $dependValue
     * @param int $type = 1 is option_id 2 is option_type_id
     * @return array
     */
    protected function createBoilerplateDco(
        &$incrementId,
        $productId,
        $optionId,
        $dependValue = NULL,
        $type = 1
    ) {
        $boilerplate = [
            'increment_id' => $incrementId,
            'product_id' => $productId,
            'depend_value' => $dependValue
        ];
        if ($type === 1) {
            $boilerplate['option_id'] = $optionId;
            $boilerplate['option_type_id'] = NULL;
        } elseif ($type === 2) {
            $boilerplate['option_id'] = NULL;
            $boilerplate['option_type_id'] = $optionId;
        }
        $incrementId++;
        return $boilerplate;
    }

    /**
     * @param array $dependOn
     * @param $productId
     * @return bool|int|string
     */
    protected function getExactOptionId($dependOn, $productId)
    {
        $optionTitles = $this->dcoResource->getOptionTitlesbyProduct($productId);

        $title = $dependOn[0] ?? '';
        $type = $dependOn[1] ?? '';
        foreach ($optionTitles as $optionTitle) {
            if ($optionTitle['type'] == $type
                && $optionTitle['title'] == $title
            ) {
                return $optionTitle['option_id'] ?? false;
            }
        }
        return false;
    }

    /**
     * @param array|string $dependOn
     * @param $optionId
     * @return bool
     */
    protected function getExactOptionTypeId($dependOn, $optionId)
    {
        $optionValues = $this->dcoResource->getOptionValuesType();
        if (is_array($dependOn) && !isset($dependOn[2])) {
            return false;
        }
        foreach ($optionValues as $optionValue) {
            if ($optionValue['option_id'] != $optionId) {
                continue;
            }
            $title = is_array($dependOn) && isset($dependOn[2]) ? $dependOn[2] : (is_string($dependOn) ? $dependOn : '');
            if ($optionValue['title'] == $title) {
                return $optionValue['option_type_id'];
            }
        }
        return false;
    }

    /**
     * @param int $optionId
     * @param bool $objectReturn
     * @return DependentOptionInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByOptionId(
        int $optionId,
        bool $objectReturn = true
    ) {
        /** @var Option $catalogOption */
        $catalogOption = $this->optionFactory->create();
        $this->optionResource->load($catalogOption, $optionId);
        if (!$catalogOption->getId()) {
            throw new InputException(__('Invalid Option ID'));
        }
        $product = $this->productRepository->getById($catalogOption->getProductId());
        $dependData = $this->dependOptionFactory->create()->loadByOptionId($optionId);
        $entityId = $dependData ? (int)$dependData->getData('dependent_id') : 0;
        $dcoRequire = $dependData ? $dependData->getData('bss_dco_require') : '';

        /** @var DependentOptionInterface $dcoOption */
        $dcoOption = $this->dependentOptionTemplate->create();
        $dcoOption->setDependentId($entityId);
        $dcoOption->setDcoRequired($dcoRequire ?? '');
        $dcoOption->setProductSku($product->getSku());
        $persistedOption = $this->getPersistedOption($product, $optionId);
        if (!$persistedOption->getProductSku()) {
            $persistedOption->setProductSku($product->getSku());
        }
        $this->converter->convertOption($persistedOption, $dcoOption);

        $values = $persistedOption->getValues();
        $dcoValues = [];
        foreach ($values as $value) {
            /** @var DependentOptionValuesInterface $dcoOptionValuesTemplate */
            $dcoOptionValuesTemplate = $this->dependentOptionValuesTemplate->create();
            $this->converter->convertOptionValues($value, $dcoOptionValuesTemplate);
            $dependData = $this->dependOptionFactory->create()->loadByOptionTyeId($value->getOptionTypeId());
            if ($dependData) {
                $entityId = $this->getDependData(
                    $dependData->getData(),
                    DependentOptionValuesInterface::KEY_DEPENDENT_ID
                );
                $dependentOnId = $this->getDependData(
                    $dependData->getData(),
                    DependentOptionValuesInterface::KEY_DEPEND_VALUE
                );
                $optionTypeId = $this->getDependData(
                    $dependData->getData(),
                    DependentOptionValuesInterface::KEY_OPTION_TYPE_ID
                );
                $dependentValue = $this->getDependValue($dependentOnId, $product->getId());
                $dcoOptionValuesTemplate->setDependValue($dependentValue);
                $dcoOptionValuesTemplate->setDependentId((int)$entityId);
                $dcoOptionValuesTemplate->setOptionTypeId((int)$optionTypeId);
            }
            $dcoValues[] = $objectReturn ? $dcoOptionValuesTemplate : $dcoOptionValuesTemplate->getData();
        }
        $dcoOption->setValues($dcoValues);
        return $objectReturn ? $dcoOption : $dcoOption->getData();
    }

    /**
     * @param ProductInterface $product
     * @param int|string $searchId
     * @return ProductCustomOptionInterface
     */
    protected function getPersistedOption(
        ProductInterface $product,
        int $searchId
    ) {
        $options = $this->getProductOptions($product);
        $persistedOption = array_filter($options, function ($iOption) use ($searchId) {
            return $searchId == $iOption->getOptionId();
        });
        $persistedOption = reset($persistedOption);
        return $persistedOption;
    }

    /**
     * @param ProductInterface|Product $product
     * @param bool $requiredOnly
     * @return ProductCustomOptionInterface[]
     */
    protected function getProductOptions(
        ProductInterface $product,
        $requiredOnly = false
    ) {
        $key = $product->getId() . '_' . $product->getStoreId() . '_' . (int)$requiredOnly;
        if (!isset($this->productOptions[$key])) {
            $this->productOptions[$key] = $this->collectionFactory->create()->getProductOptions(
                $product->getEntityId(),
                $product->getStoreId(),
                $requiredOnly
            );
        }
        return $this->productOptions[$key];
    }

    /**
     * @param array $dependData
     * @param string $key
     * @return string
     */
    protected function getDependData(
        array $dependData,
        string $key
    ) {
        return $dependData[$key] ?? '0';
    }

    /**
     * @param string|int $dependentOnId
     * @param int $productId
     * @return string
     * @throws LocalizedException
     */
    protected function getDependValue($dependentOnId, $productId)
    {
        $dependentOnId = explode(',', $dependentOnId);
        $row = $this->dcoResource->loadDcoByOption($productId, $dependentOnId, 3);
        if (!$row || empty($row)) {
            return '';
        }
        $dependValue = '';
        foreach ($row as $rowValue) {
            if (isset($rowValue['option_type_id']) && $rowValue['option_type_id']) {
                /** @var Value $optionValue */
                $optionValue = $this->valueFactory->create();
                $this->valueResource->load($optionValue, $rowValue['option_type_id']);
                $optionTypeTitle = $this->dcoResource->getTitle($rowValue['option_type_id'], $type = 2);
                if ($optionValue->getId()) {
                    $optionTitle = $this->dcoResource->getTitle($optionValue->getOptionId(), $type = 1);
                    /** @var Option $option */
                    $option = $this->optionFactory->create();
                    $this->optionResource->load($option, $optionValue->getOptionId());
                    $dependValue .= self::KEY_SYMBOL_SEPARATE_VALUE . $optionTitle . self::KEY_SYMBOL_SEPARATE . $option->getType() . self::KEY_SYMBOL_SEPARATE . $optionTypeTitle;
                }
            } elseif (isset($rowValue['option_id']) && $rowValue['option_id']) {
                /** @var Option $option */
                $option = $this->optionFactory->create();
                $this->optionResource->load($option, $rowValue['option_id']);
                $optionTitle = $this->dcoResource->getTitle($rowValue['option_id'], $type = 1);
                if ($option->getId()) {
                    $dependValue .= $optionTitle . self::KEY_SYMBOL_SEPARATE . $option->getType();
                }
            }
        }
        $dependValue = trim($dependValue, self::KEY_SYMBOL_SEPARATE_VALUE);
        return $dependValue;
    }
}
