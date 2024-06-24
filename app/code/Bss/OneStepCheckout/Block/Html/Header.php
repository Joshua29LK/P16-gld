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
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OneStepCheckout\Block\Html;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template\Context;

/**
 * @package Bss\OneStepCheckout\Block\Html
 */
class Header extends \Magento\Theme\Block\Html\Header
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'Bss_OneStepCheckout::html/header.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Escaper $escaper
     */
    protected $escaper;

    /**
     * Construct
     *
     * @param \Magento\Customer\Model\Session $session
     * @param Context $context
     * @param array $data
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        Context $context,
        \Magento\Framework\Escaper $escaper,
        array $data = []
    ) {
        $this->session = $session;
        $this->escaper = $escaper;
        parent::__construct($context, $data);
    }

    /**
     * Check is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->session->isLoggedIn();
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->escaper;
    }

}
