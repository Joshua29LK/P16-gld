<?php

namespace Woom\CmsTree\Ui\Component\Form\Field;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Ui\Component\Form\Field;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Framework\Exception\LocalizedException;

class MenuPosition extends Field
{
    /**
     * Context
     *
     * @var ContextInterface
     */
    protected $context;

    /**
     * Data Provider
     *
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * MenuPosition constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManager       $storeManager
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManager $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->context = $context;
        $this->dataProvider = $context->getDataProvider();
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     * @throws LocalizedException
     */
    public function prepare()
    {
        parent::prepare();
        $pageId = $this->context->getRequestParam('page_id');
        $parentId = $this->context->getRequestParam('parent');
        $dataProviderData = $this->dataProvider->getData();

        $showComponent = false;

        //if parent page is in root ids (parent is root), show
        if ($parentId && in_array($parentId, $this->getRootIds())) {
            $showComponent = true;
        }

        //if page id exists and is level 2, show
        if ($pageId
            && isset($dataProviderData[$pageId])
            && isset($dataProviderData[$pageId]['level'])
            && ($dataProviderData[$pageId]['level'] == 2)
        ) {
            $showComponent = true;
        }

        if (!$showComponent) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }

    /**
     * Return ids of root pages as array
     *
     * @return array
     */
    public function getRootIds()
    {
        $ids = [TreeInterface::TREE_ROOT_ID];
        foreach ($this->storeManager->getStores(true) as $store) {
            $ids[] = $store->getRootCmsTreeId();
        }

        return $ids;
    }
}
