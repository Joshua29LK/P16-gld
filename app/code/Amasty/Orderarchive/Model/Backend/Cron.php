<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/
namespace Amasty\Orderarchive\Model\Backend;

use Amasty\Orderarchive\Model\Cron\FrequencyScheduleResolver;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Cron extends Value
{
    /**
     * Cron string path
     */
    private const CRON_STRING_PATH = 'crontab/default/jobs/orderarchive_archiving/schedule/cron_expr';

    /**
     * @var ValueFactory
     */
    private $configValueFactory;

    /**
     * @var FrequencyScheduleResolver
     */
    private $frequencyScheduleResolver;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        FrequencyScheduleResolver $frequencyScheduleResolver,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );

        $this->configValueFactory = $configValueFactory;
        $this->frequencyScheduleResolver = $frequencyScheduleResolver;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function afterSave()
    {
        $frequency = $this->getValue();
        $cronExprString = $this->frequencyScheduleResolver->getSchedule((string)$frequency);

        try {
            $this->configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t save the cron expression.'));
        }

        return parent::afterSave();
    }
}
