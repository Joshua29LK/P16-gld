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
namespace Bss\Faqs\Block\Widget\Grid\Column;

class RendererStore extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store
{
    /**
     * Render row store views
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $out = '';
        $scopes = [];
        foreach ($row->getStoreName() as $k => $label) {
            $scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
        }
        $out .= implode('<br/>', $scopes);
        return $out;
    }
}
