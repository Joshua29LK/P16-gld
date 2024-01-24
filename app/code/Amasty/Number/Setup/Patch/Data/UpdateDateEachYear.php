<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Setup\Patch\Data;

use Amasty\Number\Api\Data\CounterInterface;
use Amasty\Number\Model\Counter\CounterRepository;
use Amasty\Number\Model\Counter\ResourceModel\Counter as CounterResource;
use Amasty\Number\Model\Number\Format\DateFormatter;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateDateEachYear implements DataPatchInterface
{
    private const UNIX_TIME = '1970-01-01%';
    private const DATE_YEAR = 'Y-01-01';

    /**
     * @var CounterResource
     */
    private $counterResource;

    /**
     * @var CounterResource\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CounterRepository
     */
    private $counterRepository;

    /**
     * @var DateFormatter
     */
    private $dateFormatter;

    public function __construct(
        CounterResource   $counterResource,
        CounterResource\CollectionFactory $collectionFactory,
        CounterRepository $counterRepository,
        DateFormatter $dateFormatter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->counterResource = $counterResource;
        $this->counterRepository = $counterRepository;
        $this->dateFormatter = $dateFormatter;
    }

    public function apply(): void
    {
        $counterCollection = $this->collectionFactory->create();
        $counterCollection->addFieldToFilter(CounterInterface::UPDATED_AT, ['like' => self::UNIX_TIME]);

        foreach ($counterCollection as $counter) {
            $counterResetDate = $this->dateFormatter->formatDate(self::DATE_YEAR);
            $counter->setUpdatedAt($counterResetDate);
            try {
                $this->counterResource->save($counter);
            } catch (AlreadyExistsException $e) {
                continue;
            }
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
