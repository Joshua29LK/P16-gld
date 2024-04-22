<?php

/**
 * MageDelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category MageDelight
 * @package Magedelight_Megamenu
 * @copyright Copyright (c) 2023 Magedelight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Megamenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 *
 * @package Magedelight\Megamenu\Controller\Adminhtml\Menu
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magedelight_Megamenu::save';

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magedelight\Megamenu\Model\Menu
     */
    protected $menuModel;

    /**
     * @var \Magedelight\Megamenu\Model\MenuItems
     */
    protected $menuItemModel;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param \Magedelight\Megamenu\Model\Menu $menuModel
     * @param \Magedelight\Megamenu\Model\MenuItems $menuItemModel
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        \Magedelight\Megamenu\Model\Menu $menuModel,
        \Magedelight\Megamenu\Model\MenuItems $menuItemModel
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->menuModel = $menuModel;
        $this->menuItemModel = $menuItemModel;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $menuData = json_decode($data['menu_data_json'], true);

            if ($data['totalMenus'] > 0 && is_null($menuData)) {
                $this->messageManager->addErrorMessage(
                    __("Something went wrong while saving data, please try again")
                );
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['menu_id' => $this->getRequest()->getParam('menu_id')]
                );
            }

            if (is_array($menuData) && count($menuData) > 0) {
                $menuDataFinal = $menuData['menu_data'];
            }
            $data = $this->dataProcessor->filter($data);

            $isActive = isset($data['is_active']) && !empty($data['is_active']) ? 1 : 0;
            $data['is_active'] = $isActive;

            $data['menu_id'] = empty($data['menu_id']) ? null : $data['menu_id'];

            if (isset($data['store_id']) && (in_array("0", $data['store_id']))) {
                unset($data['store_id']);
                $data['store_id'] = [0];
            }

            if ($data['customer_groups']) {
                $data['customer_groups'] = implode(',', $data['customer_groups']);
            }

            /** @var \Magedelight\Megamenu\Model\Menu $model */
            $model = $this->menuModel;

            $id = $this->getRequest()->getParam('menu_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);
            $this->_eventManager->dispatch(
                'megamenu_menu_prepare_save',
                ['menu' => $model, 'request' => $this->getRequest()]
            );

            if (!$this->dataProcessor->validateRequireEntry($data)) {
                return $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getMenuId(),
                    '_current' => true]);
            }

            try {
                $form = $model->save();

                $menuId = $form->getMenuId();
                $deleteItems = $this->menuItemModel->deleteItems($menuId);

                if (is_array($menuData) && count($menuData) > 0) {
                    $this->processMenuData($menuDataFinal, $menuId);
                }
                $this->messageManager->addSuccessMessage(__('You saved the menu.'));
                $this->dataPersistor->clear('megamenu_menu');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getMenuId(),
                        '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }

            $this->dataPersistor->set('megamenu_menu', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['menu_id' => $this->getRequest()->getParam('menu_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function processMenuData(array $menuDataFinal, $menuId)
    {
        foreach ($menuDataFinal as $i => $menu_item_data) {
            $menu_data = $menuDataFinal[$i];
            if (isset($menu_data['item_name']) && isset($menu_data['item_type'])) {
                $itemsData = $this->prepareMenuItemData($menu_data, $menuId);
                $currentItem = $this->menuItemModel->setData($itemsData)->save();
                $itemId = $currentItem->getItemId();

                foreach ($menuDataFinal as $key => $val) {
                    if (isset($val['item_parent_id']) && $val['item_parent_id'] == $i) {
                        $menuDataFinal[$key]['item_parent_id'] = $itemId;
                    }
                }
            }
        }
    }

    private function prepareMenuItemData(array $menu_data, $menuId)
    {
        $itemsData = [
            'item_name' => $menu_data['item_name'],
            'item_type' => $menu_data['item_type'],
            'sort_order' => $menu_data['sort_order'],
            'item_parent_id' => $menu_data['item_parent_id'],
            'menu_id' => $menuId,
            'object_id' => $menu_data['object_id'],
            'item_link' => $menu_data['item_link'],
            'item_font_icon' => $menu_data['item_font_icon'],
            'item_class' => $menu_data['item_class'],
            'animation_option' => $menu_data['animation_option'],
            'category_display' => $menu_data['item_all_cat'] ?: null,
            'category_vertical_menu' => $menu_data['item_vertical_menu'] ?: null,
            'category_vertical_menu_bg' => $menu_data['vertical_menu_bgcolor'] ?: null,
            'item_columns' => !empty($menu_data['item_columns']) ? json_encode($menu_data['item_columns']) : null,
            'category_columns' => !empty($menu_data['category_columns']) ? json_encode($menu_data['category_columns']) : null,
            'vertical_cat_exclude' => !empty($menu_data['verticalcatexclude']) ? $menu_data['verticalcatexclude'] : null,
            'vertical_cat_sortby' => !empty($menu_data['vertical_cat_sortby']) ? $menu_data['vertical_cat_sortby'] : null,
            'vertical_cat_sortorder' => !empty($menu_data['vertical_cat_sortorder']) ? $menu_data['vertical_cat_sortorder'] : null,
            'vertical_cat_level' => !empty($menu_data['vertical_cat_level']) ? $menu_data['vertical_cat_level'] : null,
            'product_display' => !empty($menu_data['product_display']) ? $menu_data['product_display'] : null,
            'open_in_new_tab' => !empty($menu_data['open_in_new_tab']) ? $menu_data['open_in_new_tab'] : null,
        ];

        return $itemsData;
    }
}
