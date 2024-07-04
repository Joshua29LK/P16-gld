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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Block\Widget\Grid;

class Multistore extends \Magento\Backend\Block\Widget\Grid\Column\Multistore
{
    /**
     * @var string[]
     */
    protected $_rendererTypes = [
        'store' => \Bss\Faqs\Block\Widget\Grid\Column\RendererStore::class
    ];

    /**
     * @var array
     */
    protected $_filterTypes = [
        'store' => \Bss\Faqs\Block\Widget\Grid\Column\FilterStore::class
    ];
}
