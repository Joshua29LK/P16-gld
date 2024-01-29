<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Setup\Operation;

use Amasty\Number\Exceptions\InvalidNumberFormat;
use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Counter\CounterRepository;
use Amasty\Number\Model\Number\Validator;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\StringUtils;

class MoveDataFromConfig
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var StringUtils
     */
    private $stringUtils;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CounterRepository
     */
    private $counterRepository;

    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;

    public function __construct(
        Validator $validator,
        StringUtils $stringUtils,
        ConfigInterface $config,
        CounterRepository $counterRepository,
        CollectionFactory $configCollectionFactory
    ) {
        $this->validator = $validator;
        $this->stringUtils = $stringUtils;
        $this->config = $config;
        $this->counterRepository = $counterRepository;
        $this->configCollectionFactory = $configCollectionFactory;
    }

    public function execute()
    {
        /** @var Value $configCounter */
        foreach ($this->getExistingCountersByField(ConfigProvider::PART_NUMBER_COUNTER) as $configCounter) {
            if ($entityType = $this->resolveEntityTypeByPath($configCounter->getPath())) {
                $newCounter = $this->counterRepository->create()
                    ->setScopeId($configCounter->getScopeId())
                    ->setScopeTypeId($configCounter->getScope())
                    ->setEntityTypeId($entityType)
                    ->setCurrentValue($configCounter->getValue());
                $this->counterRepository->save($newCounter);
            }
        }

        foreach ($this->getExistingCountersByField(ConfigProvider::PART_NUMBER_FORMAT) as $configCounter) {
            $entityType = $this->resolveEntityTypeByPath($configCounter->getPath());

            try {
                $this->validator->validatePattern($entityType, $configCounter->getValue());
            } catch (InvalidNumberFormat $e) {
                if (isset(Validator::EXAMPLE_FORMATS[$entityType])) {
                    $configCounter->setValue(Validator::EXAMPLE_FORMATS[$entityType]);
                    $this->config->saveConfig(
                        $configCounter->getPath(),
                        $configCounter->getValue(),
                        $configCounter->getScope(),
                        $configCounter->getScopeId()
                    );
                }
            }
        }
    }

    /**
     * @param string $field
     * @return array|DataObject[]
     */
    private function getExistingCountersByField(string $field = '')
    {
        $entityCounters = [];
        $configCollection = $this->configCollectionFactory->create();

        foreach (ConfigProvider::AVAILABLE_ENTITY_TYPES as $entityType) {
            $entityCounters[] = ConfigProvider::XPATH_PREFIX . $entityType . $field;
        }

        if (count($entityCounters) > 0) {
            $configCollection->addFieldToFilter('path', ['in' => $entityCounters]);

            return $configCollection->getItems();
        }

        return [];
    }

    /**
     * @param string $path
     * @return mixed|null
     */
    private function resolveEntityTypeByPath(string $path)
    {
        foreach (ConfigProvider::AVAILABLE_ENTITY_TYPES as $entityType) {
            $searchPath = ConfigProvider::XPATH_PREFIX . $entityType . '/';

            if ($this->stringUtils->strpos($path, $searchPath) !== false) {
                return $entityType;
            }
        }

        return null;
    }
}
