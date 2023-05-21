<?php

namespace Geissweb\Euvat\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ValidationActions provides the actions for the listing
 */
class ValidationActions extends Column
{
    /** Url paths */
    const URL_PATH_VALIDATE = 'euvat/validation/revalidate';
    const URL_PATH_DELETE = 'euvat/validation/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ValidationActions constructor.
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
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');

                if (isset($item['validation_id'])) {
                    $item[$name]['validate'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_VALIDATE, ['id' => $item['validation_id']]),
                        'label' => __('Validate again')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, ['id' => $item['validation_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __(sprintf('Delete validation data for VAT number %s', $item['vat_id'])),
                            'message' => __('Are you sure you wan\'t to delete the validation data?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
