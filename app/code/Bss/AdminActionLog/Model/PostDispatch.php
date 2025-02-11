<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AdminActionLog
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\AdminActionLog\Model;

use Magento\Catalog\Helper\Product\Edit\Action\Attribute;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

class PostDispatch
{
    /**
     * @var Data|null
     */
    protected $jsonHelper = null;

    /**
     * @var Attribute|null
     */
    protected $actionAttribute = null;

    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Structure
     */
    protected $structureConfig;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ActionDetailFactory
     */
    protected $logdetail;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $theme;

    /**
     * PostDispatch constructor.
     * @param Structure $structureConfig
     * @param ManagerInterface $messageManager
     * @param Data $jsonHelper
     * @param Attribute $actionAttribute
     * @param Registry $coreRegistry
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ActionDetailFactory $logdetail
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $theme
     */
    public function __construct(
        \Magento\Config\Model\Config\Structure                     $structureConfig,
        \Magento\Framework\Message\ManagerInterface                $messageManager,
        \Magento\Framework\Json\Helper\Data                        $jsonHelper,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute      $actionAttribute,
        \Magento\Framework\Registry                                $coreRegistry,
        \Magento\Framework\App\RequestInterface                    $request,
        \Magento\Framework\App\ResponseInterface                   $response,
        \Bss\AdminActionLog\Model\ActionDetailFactory              $logdetail,
        \Magento\Store\Model\StoreManagerInterface                 $storeManager,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $theme
    ) {
        $this->structureConfig = $structureConfig;
        $this->messageManager = $messageManager;
        $this->coreRegistry = $coreRegistry;
        $this->jsonHelper = $jsonHelper;
        $this->actionAttribute = $actionAttribute;
        $this->request = $request;
        $this->response = $response;
        $this->logdetail = $logdetail;
        $this->storeManager = $storeManager;
        $this->theme = $theme;
    }

    /**
     * Generic
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function Generic($config, $eventModel)
    {
        return true;
    }

    /**
     * MassAction
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function MassAction($config, $eventModel)
    {
        $selected = $this->request->getParam('selected');
        $info = '';
        if (is_array($selected) && !empty($selected)) {
            $info = 'Ids: ' . implode(',', $selected);
        }
        $eventModel->setInfo($info);
        return true;
    }

    /**
     * ConfigView
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function ConfigView($config, $eventModel)
    {
        $section = $this->request->getParam('section');
        if (!$section) {
            $section = 'General';
        }
        $eventModel->setInfo(ucwords(str_replace("_", " ", $section)));
        return true;
    }

    /**
     * Category Move
     *
     * @param $config
     * @param $eventModel
     * @return mixed
     */
    public function CategoryMove($config, $eventModel)
    {
        return $eventModel->setInfo('Category#' . $this->request->getParam('id'));
    }

    /**
     * Global Search
     *
     * @param $config
     * @param $eventModel
     * @return mixed
     */
    public function GlobalSearch($config, $eventModel)
    {
        return $eventModel->setInfo($this->request->getParam('query'));
    }

    /**
     * Forgot Password
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function ForgotPassword($config, $eventModel)
    {
        if ($this->request->isPost()) {
            $info = $this->request->getParam('email');
            $messages = $this->messageManager->getMessages()->getLastAddedMessage();
            $result = ('error' != $messages->getType()) ? true : false;
            return $eventModel->setResult($result)->setInfo($info);
        }
        return false;
    }

    /**
     * Customer Validate
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function CustomerValidate($config, $eventModel)
    {
        $out = json_decode($this->response->getBody());
        if (!empty($out->error)) {
            $customerId = $this->request->getParam('id');
            return $eventModel->setResult(false)->setInfo($customerId == 0 ? '' : $customerId);
        }
        return false;
    }

    /**
     * Promo Catalog Apply
     *
     * @param $config
     * @param $eventModel
     * @return mixed
     */
    public function PromoCatalogApply($config, $eventModel)
    {
        return $eventModel->setInfo(
            $this->request->getParam('rule_id') ? 'Id :' . $this->request->getParam('rule_id') : 'All rules'
        );
    }

    /**
     * Newsletter Unsubscribe
     *
     * @param $config
     * @param $eventModel
     * @return mixed
     */
    public function NewsletterUnsubscribe($config, $eventModel)
    {
        $subscriberId = $this->request->getParam('subscriber');
        if (is_array($subscriberId)) {
            $subscriberId = implode(', ', $subscriberId);
        }
        return $eventModel->setInfo($subscriberId);
    }

    /**
     * Tax Rates Import
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function TaxRatesImport($config, $eventModel)
    {
        if (!$this->request->isPost()) {
            return false;
        }

        $messages = $this->messageManager->getMessages()->getLastAddedMessage();
        $result = ('error' != $messages->getType()) ? true : false;

        return $eventModel->setResult($result);
    }

    /**
     * Product Update Attributes
     *
     * @param $config
     * @param $eventModel
     * @param $log
     * @return mixed
     */
    public function ProductUpdateAttributes($config, $eventModel, $log)
    {
        $change = $this->logdetail->create();
        $products = $this->request->getParam('product');
        if (!$products) {
            $products = $this->actionAttribute->getProductIds();
        }
        if ($products) {
            $log->addActionsDetail(
                clone $change->setSourceName(
                    'product'
                )->setOldValue(
                    []
                )->setNewValue(
                    ['ids' => implode(', ', $products)]
                )
            );
        }

        $log->addActionsDetail(
            clone $change->setSourceName(
                'inventory'
            )->setOldValue(
                []
            )->setNewValue(
                $this->request->getParam('inventory', [])
            )
        );
        $attributes = $this->request->getParam('attributes', []);
        $status = $this->request->getParam('status', null);
        if (!$attributes && $status) {
            $attributes['status'] = $status;
        }
        $log->addActionsDetail(
            clone $change->setSourceName('attributes')->setOldValue([])->setNewValue($attributes)
        );

        $websiteIds = $this->request->getParam('remove_website', []);
        if ($websiteIds) {
            $log->addActionsDetail(
                clone $change->setSourceName(
                    'remove_website_ids'
                )->setOldValue(
                    []
                )->setNewValue(
                    ['ids' => implode(', ', $websiteIds)]
                )
            );
        }

        $websiteIds = $this->request->getParam('add_website', []);
        if ($websiteIds) {
            $log->addActionsDetail(
                clone $change->setSourceName(
                    'add_website_ids'
                )->setOldValue(
                    []
                )->setNewValue(
                    ['ids' => implode(', ', $websiteIds)]
                )
            );
        }
        if (!is_array($products) || !empty($products)) {
            $products = $this->request->getParam('selected');
        }

        $info = __('Attributes Updated');
        if (is_array($products) && !empty($products)) {
            $info = 'Ids: ' . implode(',', $products);
        }
        return $eventModel->setInfo($info);
    }

    /**
     * Tax Class Save
     *
     * @param $config
     * @param $eventModel
     * @return bool|mixed
     */
    public function TaxClassSave($config, $eventModel)
    {
        if (!$this->request->isPost()) {
            return false;
        }
        $classType = $this->request->getParam('class_type');
        $classId = (int)$this->request->getParam('class_id');

        return $this->LogTaxClassEvent($classType, $eventModel, $classId);
    }

    /**
     * Tax Class Delete
     *
     * @param $config
     * @param $eventModel
     * @return bool|mixed
     */
    public function TaxClassDelete($config, $eventModel)
    {
        if (!$this->request->isPost()) {
            return false;
        }
        $classId = (int)$this->request->getParam('class_id');
        return $this->LogTaxClassEvent('', $eventModel, $classId);
    }

    /**
     * Reindex Process
     *
     * @param $config
     * @param $eventModel
     * @return bool
     */
    public function ReindexProcess($config, $eventModel)
    {
        $processIds = $this->request->getParam('process', null);
        if (!$processIds) {
            return false;
        }
        return $eventModel->setInfo(is_array($processIds) ? implode(', ', $processIds) : (int)$processIds);
    }

    /**
     * System Currency Save
     *
     * @param $config
     * @param $eventModel
     * @param $log
     * @return bool
     */
    public function SystemCurrencySave($config, $eventModel, $log)
    {
        $change = $this->logdetail->create();
        $data = $this->request->getParam('rate');
        $values = [];
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $currencyCode => $rate) {
            foreach ($rate as $currencyTo => $value) {
                $value = $value ?? abs((float) $value);
                if ($value == 0) {
                    continue;
                }
                $values[] = $currencyCode . '=>' . $currencyTo . ': ' . $value;
            }
        }

        $log->addActionsDetail(
            $change->setSourceName(
                'rates'
            )->setOldValue(
                []
            )->setNewValue(
                ['rates' => implode(', ', $values)]
            )
        );

        $messages = $this->messageManager->getMessages()->getLastAddedMessage();
        $result = ('error' != $messages->getType()) ? true : false;

        return $eventModel->setResult($result);
    }

    /**
     * Design Theme
     *
     * @param $config
     * @param $eventModel
     * @param $log
     * @return false|mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function DesignTheme($config, $eventModel, $log)
    {
        $change = $this->logdetail->create();
        $data = $this->request->getParams();
        if (!is_array($data)) {
            return false;
        }
        $scope = null;
        if ($data['scope'] == 'websites') {
            $scope = $this->storeManager->getWebsite($data['scope_id']);
        } elseif ($data['scope'] == 'groups') {
            $scope = $this->storeManager->getGroup($data['scope_id']);
        } elseif ($data['scope'] == 'stores') {
            $scope = $this->storeManager->getStore($data['scope_id']);
        }
        $info = $scope->getName();
        if (array_key_exists('theme_theme_id', $data)) {
            $data['theme_theme_id'] = $this->theme->create()->getItemById($data['theme_theme_id'])['theme_title'];
        }

        $log->addActionsDetail(
            $change->setSourceName(
                'theme'
            )->setOldValue(
                []
            )->setNewValue(
                $data
            )
        );

        $messages = $this->messageManager->getMessages()->getLastAddedMessage();
        $result = ('error' != $messages->getType()) ? true : false;

        return $eventModel->setResult($result)->setInfo($info);
    }

    /**
     * Save Cache Settings
     *
     * @param $config
     * @param $eventModel
     * @param $Log
     * @return bool
     */
    public function SaveCacheSettings($config, $eventModel, $Log)
    {
        if (!$this->request->isPost()) {
            return false;
        }
        $info = '-';
        $cacheTypes = $this->request->getPost('types');
        if (is_array($cacheTypes) && !empty($cacheTypes)) {
            $cacheTypes = implode(', ', $cacheTypes);
            $info = __('Cache types: %1 ', $cacheTypes);
        }

        $messages = $this->messageManager->getMessages()->getLastAddedMessage();
        $result = ('error' != $messages->getType()) ? true : false;

        return $eventModel->setResult($result)->setInfo($info);
    }

    /**
     * Sales Archive Management
     *
     * @param $config
     * @param $eventModel
     * @return mixed
     */
    public function SalesArchiveManagement($config, $eventModel)
    {
        $ids = $this->request->getParam('order_id', $this->request->getParam('order_ids'));
        if (is_array($ids)) {
            $ids = implode(', ', $ids);
        }
        return $eventModel->setInfo($ids);
    }

    /**
     * Log Tax Class Event
     *
     * @param $classType
     * @param $eventModel
     * @param $classId
     * @return mixed
     */
    protected function LogTaxClassEvent($classType, $eventModel, $classId)
    {
        if ($classType == 'PRODUCT') {
            $eventModel->setEventCode('tax_product_tax_classes');
        }

        $success = true;
        $messages = null;
        $body = $this->response->getBody();
        if ($body) {
            $messages = $this->jsonHelper->jsonDecode($body);
        }
        if ($messages && !empty($messages['success'])) {
            $success = $messages['success'];
            if (empty($classId) && !empty($messages['class_id'])) {
                $classId = $messages['class_id'];
            }
        }

        $messageInfo = $classType . ($classId ? ': #' . $classId : '');
        return $eventModel->setResult($success)->setInfo($messageInfo);
    }

    /**
     * Action Print
     *
     * @param $config
     * @param $eventModel
     * @return false|mixed
     */
    public function actionPrint($config, $eventModel)
    {
        if ($invoiceId = $this->request->getParam('invoice_id')) {
            $invoice = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            return $eventModel->setInfo('#' . $invoice->getIncrementId());
        }
        if ($shipmentId = $this->request->getParam('shipment_id')) {
            $shipment = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            return $eventModel->setInfo('#' . $shipment->getIncrementId());
        }
        if ($creditmemoId = $this->request->getParam('creditmemo_id')) {
            $creditmemo = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
            return $eventModel->setInfo('#' . $creditmemo->getIncrementId());
        }

        return false;
    }
}
