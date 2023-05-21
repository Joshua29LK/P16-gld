<?php
/**
 * Created by RedChamps.
 * User: rav
 * Date: 21/11/18
 * Time: 5:03 PM
 */
namespace RedChamps\Core\Model;

use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Message\Renderer\RendererInterface;

/*
 * Package: Core
 * Class: HtmlMessageRenderer
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class HtmlMessageRenderer implements RendererInterface
{
    const CODE = 'rc_html_renderer';

    const MESSAGE_IDENTIFIER = 'rc_html_message';

    /**
     * @param MessageInterface $message
     * @param array $initializationData
     * @return string
     */
    public function render(MessageInterface $message, array $initializationData)
    {
        return $message->getData()['html'];
    }
}
