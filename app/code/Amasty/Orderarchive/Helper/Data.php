<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL_ENABLE_MODULE = 'orderarchive/general/enabled';

    public const CONFIG_PATH_GENERAL_DAY_AGO = 'orderarchive/general/day_ago';

    public const CONFIG_PATH_GENERAL_STATUS = 'orderarchive/general/status';

    public const CONFIG_PATH_GENERAL_FREQUENCY = 'orderarchive/general/frequency';

    public const CONFIG_PATH_GENERAL_ENABLE_MASSFILTER = 'orderarchive/general/enable_massfilter';

    public const CONFIG_PATH_EMAIL_ENABLE = 'orderarchive/email/enabled';

    public const CONFIG_PATH_EMAIL_TEMPLATE = 'orderarchive/email/template';

    public const CONFIG_PATH_EMAIL_RECIPIENT = 'orderarchive/email/recipient';

    public const SALES_ORDER_GRID_NAMESPACE = 'sales_order_grid';

    public const ORDERSPRO_ORDER_GRID = 'orderspro_order';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|null
     */
    protected $_scopeConfig = null;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $string
     * @return mixed
     */
    public function stringValidationAndCovertInArray($string)
    {
        $validate = function ($urls) {
            return preg_split('|\s*[\r\n]+\s*|', $urls, -1, PREG_SPLIT_NO_EMPTY);
        };

        return $validate($string);
    }

    /**
     * @return bool
     */
    public function isModuleOn()
    {
        return $this->getConfigValueByPath(
            self::CONFIG_PATH_GENERAL_ENABLE_MODULE,
            null,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ? true : false;
    }

    /**
     * @param $path
     * @param null $storeId
     * @param string $scope
     * @return mixed
     */
    public function getConfigValueByPath(
        $path,
        $storeId = null,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->_scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @return array
     */
    public function getArchiveListModel()
    {
        return [
            'amasty_orderarchive_sales_order_grid_archive',
            'amasty_orderarchive_sales_shipment_grid_archive',
            'amasty_orderarchive_sales_invoice_grid_archive',
            'amasty_orderarchive_sales_creditmemo_grid_archive'
        ];
    }

    /**
     * @param \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface $result
     * @param string $method
     * @return \Magento\Framework\Phrase|string
     */
    public function getInformationString(
        $result,
        $method = \Amasty\Orderarchive\Controller\Adminhtml\Archive::ADD_TO_ARCHIVE_METHOD_CODE
    ) {
        $resultStr = '';

        if ($result instanceof \Magento\Framework\DataObject) {
            foreach ($result->toArray() as $key => $item) {
                if ($item) {
                    $stringBegin = __(ucfirst($key) . 's Id\'s: ');
                    $itemsToStr = implode(', ', $item);
                    $stringEnd = '!' . PHP_EOL;
                    switch ($method) {
                        case \Amasty\Orderarchive\Controller\Adminhtml\Archive::ADD_TO_ARCHIVE_METHOD_CODE:
                            $methodText = __('Was Added To Archive');
                            break;
                        case \Amasty\Orderarchive\Controller\Adminhtml\Archive::REMOVE_PERMANENTLY_METHOD_CODE:
                            $methodText = __('Was Removed Permanently');
                            break;
                        case \Amasty\Orderarchive\Controller\Adminhtml\Archive::REMOVE_FROM_ARCHIVE_METHOD_CODE:
                            $methodText = __('Was Removed From Archive');
                            break;
                        default:
                            $methodText = __('Was Added To Archive');
                            break;
                    }

                    $resultStr .= $stringBegin . $itemsToStr . ' ' . $methodText . $stringEnd;
                }
            }
        }
        if (empty($resultStr)) {
            return __('Orders in not archived!');
        }

        return $resultStr;
    }
}
