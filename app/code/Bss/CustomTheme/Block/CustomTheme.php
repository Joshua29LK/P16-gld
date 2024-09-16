<?php
namespace Bss\CustomTheme\Block;

use Magento\Framework\View\Element\Template;
use Magmodules\WebwinkelKeur\Helper\Reviews as ReviewsHelper;

class CustomTheme extends \Magento\Customer\Block\Account\Navigation
{
    /**
     * @var ReviewsHelper
     */
    protected $reviewHelper;

    /**
     * @param Template\Context $context
     * @param ReviewsHelper $reviewHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ReviewsHelper $reviewHelper,
        array $data = []
    ) {
        $this->reviewHelper = $reviewHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSummaryData()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $summaryData = $this->reviewHelper->getSummaryData($storeId);
        if ($summaryData) {
            $iso = $this->getData('webwinkel_url');
            if (empty($iso)) {
                $iso = 'default';
            }

            $summaryData['review_url'] = '#';

            foreach ($summaryData['link'] as $key => $url) {
                if ($key == $iso) {
                    $summaryData['review_url'] = $url;
                }
            }

            $summaryData['iso'] = $iso;
        }

        return $summaryData;
    }
}
