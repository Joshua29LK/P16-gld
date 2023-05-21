<?php

namespace Woom\CmsTree\Ui\Component;

use Magento\Ui\Component\Form as MagentoForm;
use Magento\Framework\View\Element\UiComponentInterface;

class Form extends MagentoForm
{
    /**
     * @var array
     */
    private $requiredGeneralTabFields = [
        'page_id',
        'parent',
        'path',
        'store',
        'store_id',
    ];

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        $components = parent::getChildComponents();
        $this->disableComponentsForRootPage($components);

        return $components;
    }

    /**
     * Disable components for root page
     *
     * @param UiComponentInterface[] $components
     *
     * @return UiComponentInterface[]
     */
    private function disableComponentsForRootPage($components)
    {
        $id = $this->getContext()->getRequestParam($this->getContext()->getDataProvider()->getRequestFieldName(), null);
        $parentId = $this->getContext()->getRequestParam('parent', null);
        if (!$id && !$parentId) {
            foreach ($components as $key => $component) {
                $componentData = $component->getData();
                if (isset($componentData['config'])) {
                    //disable all fieldset components except for "general"
                    if (!in_array($key, ['general', 'message'])) {
                        $componentData['config']['componentDisabled'] = true;
                        $component->setData($componentData);
                    }
                    //disable all fields in "general" except the ones we require to add new pages
                    if ($key === 'general') {
                        foreach ($component->getChildComponents() as $generalChildKey => $generalChildComponent) {
                            if (isset($componentData['config']) && !in_array($key, $this->requiredGeneralTabFields)) {
                                $componentData['config']['componentDisabled'] = true;
                                $component->setData($componentData);
                            }
                        }
                    }
                }
            }
        }

        return $components;
    }
}