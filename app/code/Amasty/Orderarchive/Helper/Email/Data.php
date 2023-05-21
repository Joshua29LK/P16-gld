<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Orderarchive\Helper\Email;

use Amasty\Orderarchive\Helper\Data as HelperData;
use Amasty\Orderarchive\Model\ArchiveAffectedIds;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;

class Data extends AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(
        Context $context,
        HelperData $helper,
        DateTime $dateTime,
        TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->transportBuilder = $transportBuilder;
    }

    public function orderArchivedAfter(ArchiveAffectedIds $params): void
    {
        $executeTime = $this->dateTime->gmtDate();

        if ($this->helper->getConfigValueByPath(HelperData::CONFIG_PATH_EMAIL_ENABLE)) {
            $this->sendEmailArchiving(count($params['order']), $executeTime);
        }
    }

    private function send(array $templateVars): void
    {
        $sender = [
            'name' => $this->helper->getConfigValueByPath('trans_email/ident_general/name'),
            'email' => $this->helper->getConfigValueByPath('trans_email/ident_general/email')
        ];

        $recipientEmail = $this->helper->getConfigValueByPath(HelperData::CONFIG_PATH_EMAIL_RECIPIENT);
        $templateId = $this->helper->getConfigValueByPath(HelperData::CONFIG_PATH_EMAIL_TEMPLATE);

        $templateOptions = [
            'area' => Area::AREA_ADMINHTML,
            'store' => Store::DEFAULT_STORE_ID
        ];

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
            ->addTo($recipientEmail)
            ->getTransport();
        $transport->sendMessage();
    }

    private function sendEmailArchiving(int $countOrders, string $dateStart): void
    {
        /** @var /DateTime $dateStart */
        $dateStart = date_create_from_format('Y-m-d H:i:s', $dateStart);
        $dateEnd = date_create();
        $duration = $dateEnd->diff($dateStart);

        if (($duration->format('%i') == 0 && $duration->format('%s') == 0)) {
            $durationText = __('took less then a second')->render();
        } elseif ($duration->format('%i') == 0 && $duration->format('%s') != 0) {
            $durationText = $duration->format(' %s ' . __('second(s).'));
        } else {
            $durationText = $duration->format('%i' . __('minute(s)') . ' %s ' . __('second(s).'));
        }

        $params = [
            'datetime_start' => $dateStart->format('Y-m-d H:i:s'),
            'count_orders' => $countOrders,
            'duration' => $durationText
        ];

        $this->send($params);
    }
}
