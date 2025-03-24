<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bss\RequiredProduct\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Bss\RequiredProduct\Model\RequiredConfiguration;

/**
 * Class Bss\RequiredProduct\CustomerData\RequiredConfigurable
 */
class RequiredConfigurable implements SectionSourceInterface
{
    /**
     * @var RequiredConfiguration
     */
    protected $requiredConfiguration;

    /**
     * Constructor.
     *
     * @param RequiredConfiguration $requiredConfiguration
     */
    public function __construct(
        RequiredConfiguration $requiredConfiguration
    ) {
        $this->requiredConfiguration = $requiredConfiguration;
    }

    /**
     * Method getSectionData
     *
     * @return array|false|string
     */
    public function getSectionData()
    {
        return $this->requiredConfiguration->getRequiredConfigurable();
    }
}
