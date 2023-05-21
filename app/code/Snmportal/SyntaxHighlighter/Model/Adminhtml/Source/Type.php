<?php
/*
* Copyright © 2016 SNM-Portal.com. All rights reserved.
* See LICENSE.txt for license details.
*/
namespace Snmportal\SyntaxHighlighter\Model\Adminhtml\Source;

/**
 * Class Type
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getAllowedTypes() as $code => $name) {
            $options[] = ['value' => $code, 'label' => $name];
        }
        return $options;
    }

    /**
     * Allowed credit card types
     *
     * @return string[]
     */
    public function getAllowedTypes()
    {
        return [
            'magento' => __('HTML + Magento Markup'),
            'html' => __('HTML'),
            'php' => __('PHP'),
            'css' => __('CSS'),
            'xml' => __('XML')
        ];
    }
}
