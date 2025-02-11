<?php
namespace Magedelight\Megamenu\Plugin\Model\Category;

use Magento\Framework\Message\ManagerInterface;

class DataProvider
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    public function __construct(ManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    public function afterGetData(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        try {
            $category = $subject->getCurrentCategory();
            $result[$category->getId()]['visibleMegamenuTab'] = false;
            if (isset($result[$category->getId()]['level'])) {
                /* Below lines are commented to show Megamenu category block in all Category */
                /*if ($result[$category->getId()]['level'] == 2 || $result[$category->getId()]['level'] == 3) {*/
                    $result[$category->getId()]['visibleMegamenuTab'] = true;
                /*}*/
            }
        } catch (\Exception $e) {
            return $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $result;
    }
}
