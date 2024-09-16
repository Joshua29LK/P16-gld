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
namespace Bss\Faqs\Controller\Tag;

class View extends \Bss\Faqs\Controller\AbstractFaq
{
    /**
     * @var string
     */
    protected $faqTitle = 'FAQs Question with Tag';

    /**
     * Init faq view
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initFaqView()
    {
        $this->faqRepository->validateModuleEnable();
    }
}
