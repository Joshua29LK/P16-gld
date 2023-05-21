<?php

namespace Balticode\CategoryConfigurator\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class StepActions extends Column
{
    const URL_PATH_EDIT = 'balticode_categoryconfigurator/step/edit';
    const URL_PATH_DELETE = 'balticode_categoryconfigurator/step/delete';
    const URL_PATH_DETAILS = 'balticode_categoryconfigurator/step/details';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as $key => $item) {
            if (!isset($item['step_id'])) {
                continue;
            }

            $dataSource['data']['items'][$key][$this->getData('name')] = $this->getStepActionsData($item);
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getStepActionsData($item)
    {
        return [
            'edit' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_PATH_EDIT,
                    [
                        'step_id' => $item['step_id']
                    ]
                ),
                'label' => __('Edit')
            ],
            'delete' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_PATH_DELETE,
                    [
                        'step_id' => $item['step_id']
                    ]
                ),
                'label' => __('Delete'),
                'confirm' => [
                    'title' => __('Delete "${ $.$data.title }"'),
                    'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?')
                ]
            ]
        ];
    }
}
