<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Model;

use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;

/**
 * Class Superiortile\RequiredProduct\Model\RequiredConfiguration
 */
class RequiredConfiguration
{
    public const DATA_KEY = 'required_configurable';

    /**
     * @var SessionFactory
     */
    private $session;

    /**
     * @var array
     */
    private $configurableData;

    /**
     * @param SessionFactory $session
     */
    public function __construct(
        SessionFactory $session
    ) {
        $this->session = $session->create();
        $this->_construct();
    }

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->configurableData = $this->session->getData(self::DATA_KEY);
        if (!$this->configurableData) {
            $this->configurableData = [];
        }
    }

    /**
     * Add Configurable Item To Customer Session
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function addConfigurableItem($request)
    {
        $mainProductId = (int) $request->getParam('main_product');
        $requiredProductId = (int) $request->getParam('product');
        $collectionTypeId = (int) $request->getParam('type_id');

        if (!$mainProductId || !$requiredProductId || !$collectionTypeId) {
            return false;
        }

        $qty = (int) $request->getParam('qty') ?: 1;

        $collectionData = $this->configurableData[$mainProductId] ?? [];

        $collectionData[] = [
            'qty' => $qty,
            'product_id' => $requiredProductId,
            'main_product' => $mainProductId,
            'collection_type_id' => $collectionTypeId
        ];

        $this->configurableData[$mainProductId] = $collectionData;
        $this->session->setData(self::DATA_KEY, $this->configurableData);

        return true;
    }

    /**
     * Remove Configurable Item
     *
     * @param  RequestInterface|DataObject $request
     * @return bool
     */
    public function removeConfigurableItem($request)
    {
        if ($request instanceof RequestInterface) {
            $mainProductId = (int) $request->getParam('main_product');
            $collectionTypeId = (int) $request->getParam('collection_type_id');
        } else {
            $mainProductId = (int) $request->getData('main_product');
            $collectionTypeId = (int) $request->getData('collection_type_id');
        }

        if (!$mainProductId || !$collectionTypeId) {
            return false;
        }

        if (!empty($this->configurableData[$mainProductId][$collectionTypeId])) {
            unset($this->configurableData[$mainProductId][$collectionTypeId]);
        }
        $this->session->setData(self::DATA_KEY, $this->configurableData);

        return true;
    }

    /**
     * Get Required Configurable
     *
     * @param  int|null $mainProductId
     * @return array|mixed
     */
    public function getRequiredConfigurable($mainProductId = null)
    {
        if ($mainProductId) {
            return $this->configurableData[$mainProductId] ?? [];
        }

        return $this->configurableData;
    }
}
