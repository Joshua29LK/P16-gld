<?php
declare(strict_types=1);

namespace Bss\CustomOptionRequireAdmin\Plugin;

class Option
{
    /**
     * @param \Magento\Catalog\Model\Product\Option $subject
     * @param $result
     * @return false
     */
    public function afterGetIsRequire(
        \Magento\Catalog\Model\Product\Option $subject,
        $result
    ) {
        return false;
    }
}
