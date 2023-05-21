<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Escaper;

class Export extends AbstractElement
{
    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * Export constructor.
     *
     * @param ElementFactory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        ElementFactory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var Button $buttonBlock */
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock(Button::class);

        $params = ['website' => $buttonBlock->getRequest()->getParam('website')];

        $url = $this->backendUrl->getUrl('productattach/index/export', $params);
        $data = [
            'label' => __('Export Attachments CSV'),
            'onclick' => "setLocation('".$url."' )",
            'class' => '',
        ];

        $html = $buttonBlock->setData($data);

        return $html->toHtml();
    }
}
