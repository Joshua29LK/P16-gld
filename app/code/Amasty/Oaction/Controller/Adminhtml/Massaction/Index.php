<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Controller\Adminhtml\Massaction;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Index extends \Amasty\Oaction\Controller\Adminhtml\Massaction
{
    public function massAction(AbstractCollection $collection)
    {
        $request = $this->getRequest();
        $action = $request->getParam('type');
        $param = (int) $request->getParam('notify');
        $download = (bool) $this->getRequest()->getParam('download');

        if ($action == 'status') {
            $param = [
                'notify' => $request->getParam('notify'),
                'status' => $request->getParam('status'),
                'comment_text' => $request->getParam('comment_text')
            ];
        }

        if ($action == 'comment') {
            $param = $request->getParam('comment_text');
        }

        $oaction = $request->getParam('oaction');

        if ($oaction == null) {
            $oaction = [];
        } elseif (is_array($oaction)) {
            $collection->addFieldToFilter(
                $collection->getIdFieldName(),
                ['in' => array_keys($oaction)]
            );
        }

        try {
            $className = 'Amasty\Oaction\Model\Command\\' . ucfirst($action);
            $command = $this->_objectManager->create($className);
            $success = $command->execute($collection, $param, $oaction);

            if ($success) {
                //for combined actions show both messages
                $messages = explode('||', $success);

                foreach ($messages as $message) {
                    $this->messageManager->addSuccessMessage($message);
                }
            }

            // show non-critical errors to the user
            foreach ($command->getErrors() as $errorMessage) {
                $this->messageManager->addErrorMessage($errorMessage);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong.'));
        }

        $redirectUrl = $this->getUrl('sales/order/');

        if ($download) {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $response = [
                'download' => true,
                'redirectUrl' => $redirectUrl
            ];

            if (isset($command) && $command->hasResponse()) {
                $response['content'] = 'data:application/octet-stream;base64,'
                    . base64_encode($command->getResponseBody());
                $response['filename'] = $command->getResponseName();
            }
            $resultPage->setData($response);

            return $resultPage;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($redirectUrl);

        return $resultRedirect;
    }
}
