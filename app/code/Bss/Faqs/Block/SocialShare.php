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
namespace Bss\Faqs\Block;

class SocialShare extends \Bss\Faqs\Block\ModuleBlock
{
    /**
     * Get url key
     *
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * Get social share js
     *
     * @return string
     */
    public function getSocialShareJs()
    {
        $html = '<script>(function(d, s, id) {'
        . 'var js, fjs = d.getElementsByTagName(s)[0];'
        . 'if (d.getElementById(id)) return;'
        . 'js = d.createElement(s); js.id = id;'
        . 'js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.9";'
        . 'fjs.parentNode.insertBefore(js, fjs);'
        . '}(document, "script", "facebook-jssdk"));'
        . '</script>';
        return $html;
    }
}
