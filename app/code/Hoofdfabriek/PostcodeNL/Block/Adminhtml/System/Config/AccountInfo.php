<?php
namespace Hoofdfabriek\PostcodeNL\Block\Adminhtml\System\Config;

use Hoofdfabriek\PostcodeNL\Model\PostcodeNL;
use Magento\Backend\Block\Template\Context;

/**
 * Class AccountInfo
 */
class AccountInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var PostcodeNL
     */
    protected $postcodeNLApi;

    /**
     * AccountInfo constructor.
     *
     * @param Context $context
     * @param PostcodeNL $postcodeNL
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostcodeNL $postcodeNL,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->postcodeNLApi = $postcodeNL;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $accountInfo = $this->postcodeNLApi->getAccountInfo();
        $html = '';
        if (isset($accountInfo['name'])) {
            $html .= '<div class="config-additional-comment-title">' . $element->getLabel() . '</div>';
            $html .= '<div class="config-additional-comment-content"><table>';
            $html .= '<tr><td>' . __("Account") . '</td><td>' . $accountInfo['name']. '</td></tr>';
            $html .= '<tr><td>' . __("Has Access") . '</td><td>' . ($accountInfo['hasAccess'] ? __('Yes') : __('No')) . '</td></tr>';
            $html .= '<tr><td>' . __("Supported Countries") . '</td><td><ul>';

            foreach ($accountInfo['countries'] as $country) {
                switch ($country) {
                    case 'NLD':
                        $html .= '<li>' . __('Netherlands'). '</li>';
                        break;
                    case 'BEL':
                        $html .= '<li>' . __('Belgium'). '</li>';
                        break;
                }
            }

            $html .= '</ul></td></tr>';
            $html .= '</table></div>';
            $html .= '<div class="config-additional-comment-content">' . $element->getComment() . '</div>';
        } else {
            $html .= '<div class="config-additional-comment-content">' . __('To get API creds 
                <a target="_blank" href="https://www.postcode.nl/#register">register</a> or 
                <a target="_blank" href="https://www.postcode.nl/#login">login</a> to your PostcodeNL Acoount') . '</div>';
        }


        return $this->decorateRowHtml($element, $html);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     * @return string
     */
    private function decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        return sprintf(
            '<tr id="row_%s"><td colspan="3"><div class="config-additional-comment">%s</div></td></tr>',
            $element->getHtmlId(),
            $html
        );
    }
}
