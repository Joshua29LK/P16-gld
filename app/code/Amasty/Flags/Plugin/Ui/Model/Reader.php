<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Plugin\Ui\Model;

use Magento\Ui\Config\Reader as ConfigReader;

class Reader extends AbstractReader
{
    /**
     * @param ConfigReader $subject
     * @param array        $result
     *
     * @return array
     */
    public function afterRead(ConfigReader $subject, array $result): array
    {
        return $this->addMassactions($result);
    }
}
