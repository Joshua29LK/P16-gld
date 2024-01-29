<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Plugin\Ui\Model;

use Magento\Ui\Model\Manager as UiManager;

class Manager extends AbstractReader
{
    /**
     * @param UiManager $subject
     * @param array     $result
     *
     * @return array
     */
    public function afterGetData(UiManager $subject, array $result): array
    {
        return $this->updateMassactions($result);
    }
}
