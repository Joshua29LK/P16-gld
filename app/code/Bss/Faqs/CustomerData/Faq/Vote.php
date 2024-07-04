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
namespace Bss\Faqs\CustomerData\Faq;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Vote implements SectionSourceInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Bss\Faqs\Model\FaqRepository
     */
    protected $faqRepository;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $session;

    /**
     * Vote constructor.
     * @param \Magento\Customer\Model\SessionFactory $session
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Bss\Faqs\Model\FaqRepository $faqRepository
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $session,
        \Magento\Framework\Registry $coreRegistry,
        \Bss\Faqs\Model\FaqRepository $faqRepository
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->faqRepository = $faqRepository;
        $this->session = $session;
    }

    /**
     * Get section data
     *
     * @return array
     */
    public function getSectionData()
    {
        $faqId = $this->session->create()->getCurrentFaqView();
        try {
            if ($faqId !== null) {
                return $this->faqRepository->getFaqVoting($faqId);
            } else {
                throw new NoSuchEntityException(__('Registry FAQ doesn\'t exist'));
            }
        } catch (LocalizedException $e) {
            return ['like' => 0, 'unlike' => 0, 'state' => ''];
        }
    }
}
