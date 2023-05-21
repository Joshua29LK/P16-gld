<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Plugin\Ui\Model;

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
        return $this->addMassactions($result);
    }
}
