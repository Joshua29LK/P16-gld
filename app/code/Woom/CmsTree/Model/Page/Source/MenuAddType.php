<?php

namespace Woom\CmsTree\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

class MenuAddType implements OptionSourceInterface
{
    const BEFORE = 0;

    const AFTER = 1;

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::BEFORE,
                'label' => __('Before')
            ],
            [
                'value' => self::AFTER,
                'label' => __('After')
            ]
        ];
    }
}
