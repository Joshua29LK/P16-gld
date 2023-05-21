<?php
namespace Snmportal\SyntaxHighlighter\Plugin\Cms\Model\Wysiwyg;

/*
* Copyright © 2016 SNM-Portal.com. All rights reserved.
* See LICENSE.txt for license details.
*/

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Snmportal\SyntaxHighlighter\Plugin\Cms\Model\Wysiwyg
 */
class Config
{
    const ENABLED = 'snmportal_syntaxhighlighter/general/enabled';
    const SETTINGS = 'snmportal_syntaxhighlighter/general/wysi_options';
    const BGELEMENTS = 'snmportal_syntaxhighlighter/general/bgelements';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->scopeConfig = $config;
    }

    /**
     * Add TINYMCE Settings
     *
     * @param \Magento\Cms\Model\Wysiwyg\config $subject
     * @param \Magento\Framework\DataObject $config
     * @return \Magento\Framework\DataObject
     */
    public function afterGetConfig(
        /** @noinspection PhpUnusedParameterInspection */
        /** @noinspection PhpUnusedParameterInspection */
        /** @noinspection PhpUnusedParameterInspection */
        /** @noinspection PhpUnusedParameterInspection */
        \Magento\Cms\Model\Wysiwyg\config $subject,
        \Magento\Framework\DataObject $config
    ) {
        if ($this->scopeConfig->isSetFlag(self::ENABLED, ScopeInterface::SCOPE_STORE)) {
            $data = $this->scopeConfig->getValue(self::SETTINGS, ScopeInterface::SCOPE_STORE);
            if ($data) {
                $settings = json_decode($data, true);
                if (is_array($settings)) {
                    $data = $config->getData();
                    if (!isset($data['settings'])) {
                        $data['settings'] = [];
                    }
                    foreach ($settings as $v) {
                        $data['settings'][$v['name']] = $v['value'];
                    }
                    $config->setData($data);
                }
            }
        }
        return $config;
    }
}