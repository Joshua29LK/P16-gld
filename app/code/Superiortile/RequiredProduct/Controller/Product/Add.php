<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Controller\Product;

use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Superiortile\RequiredProduct\Controller\Product\Add
 */
class Add extends AbstractAjax
{
    /**
     * Execute view action
     *
     * @return Http|HttpInterface
     */
    public function execute()
    {
        try {
            if (!$this->formKeyValidator->validate($this->request)) {
                $this->messageManager->addErrorMessage(
                    __('Your session has expired')
                );
                return $this->jsonResponse([]);
            }
            $this->requiredConfiguration->addConfigurableItem($this->request);
            return $this->jsonResponse($this->requiredConfiguration->getRequiredConfigurable());
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }
}
