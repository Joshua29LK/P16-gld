<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_CORE
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\ProductPriceFormula\Block\System;

class ExportImport extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);
		
        $html .= '<div><b>Export</b>
                 <p>You can export price formulas for all existing products here. You can then use this file to upload formulas to another site, or use it as a backup. Note, formulas are associated to products by SKU. If product SKU has not been found formula will not be imported.</p>
                 <p><input type="button" value="Download File" class="action-default" onclick="window.ppfFileExport()" /></p>
                 </div>';
                 
		$html .= '<div style="margin-top:20px;"><b>Import</b>
                 <p>You can import price formulas for multiple products here. Upload a file in valid format. Only formulas for existing SKUs will be imported.</p>
                 <p><input type="file" name="product_price_formula_import" id="product_price_formula_import" /><input type="button" value="Upload File" style="margin-left:20px;" class="action-default" onclick="window.ppfFileImport(this)" /></p>
                 </div>';    
                 
        $html .= '<script type="text/javascript">
                window.ppfFileImport = function(btn){
                    var file = jQuery(\'#product_price_formula_import\');
                    if (!file.val()) {alert(\'Please select a file\'); return;}
                    btn.disabled = true;
                    jQuery("#product_price_formula_import").before("<i>Uploading... Please wait.</i>");
                    jQuery(\'<form action="'.$this->getUrl('productpriceformula/product/importAll').'" method="post" enctype="multipart/form-data">\').append(\'<input type="hidden" name="form_key" value="\'+FORM_KEY+\'" />\').append(jQuery("#product_price_formula_import")).appendTo(document.body).submit();
                }
                window.ppfFileExport = function(){
                    document.location.href = \''.$this->getUrl('productpriceformula/product/exportAll').'\';
                }
                </script>';
		
		$html .= $this->_getFooterHtml($element);
		
		return $html;
    }
}