<?php
namespace Hoofdfabriek\PostcodeNL\Controller\Lookup;

/**
 * Class Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Hoofdfabriek\PostcodeNL\Model\PostcodeNL
     */
    private $postcodeApi;

    /**
     * Index constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Hoofdfabriek\PostcodeNL\Model\PostcodeNL $postcodeNL
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Action\Context $context,
        \Hoofdfabriek\PostcodeNL\Model\PostcodeNL $postcodeNL
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->postcodeApi = $postcodeNL;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $result->setData($this->postcodeApi->lookupAddress(
            trim($this->_request->getParam('postcode')),
            trim($this->_request->getParam('houseNumber')),
            trim($this->_request->getParam('houseAddition'))
        ));
        return $result;
    }
}
