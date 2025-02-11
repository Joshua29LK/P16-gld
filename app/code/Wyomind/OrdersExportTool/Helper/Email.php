<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\OrdersExportTool\Helper;

use Magento\Framework\App\Helper\Context;
/**
 * Class email
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $directoryRead;
    public function __construct(\Wyomind\OrdersExportTool\Helper\Delegate $wyomind, \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead, Context $context)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context);
        $this->directoryRead = $directoryRead->create($this->storageHelper->getAbsoluteRootDir());
    }
    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Laminas\Mail\Exception
     */
    public function sendEmail($data = [])
    {
        if (empty($data["mail_recipients"])) {
            throw new \Exception(__("At least one recipient is required."));
        }
        if (empty($data["mail_subject"])) {
            throw new \Exception(__("The email subject is required."));
        }
        if (empty($data["mail_sender"])) {
            throw new \Exception(__("The email sender is required."));
        }
        $mails = explode(',', $data['mail_recipients']);
        foreach ($mails as $mail) {
            $this->mailWithAttachment($data["mail_sender"], $mail, $data["mail_subject"], $data["mail_message"]);
        }
    }
    /**
     * @param $mailFrom
     * @param $mailto
     * @param $subject
     * @param $message
     * @param array $filenames
     * @param $path
     * @param null $type
     * @return \Laminas\Mail\Message
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Laminas\Mail\Message
     */
    public function mailWithAttachment($mailFrom, $mailto, $subject, $message, $filenames = [], $path = null, $type = null)
    {
/*        $mail = new \Laminas\Mail\Message();
        $mail->setType(\Laminas\Mime\Mime::MULTIPART_MIXED);
        $mail->setFrom($mailFrom, $mailFrom);
        $mail->setBodyHtml($message);
        $mail->addTo($mailto, $mailto);
        $mail->setSubject($subject);
        if (!is_array($filenames)) {
            $filenames = [$filenames];
        }
        foreach ($filenames as $filename) {
            $mail->createAttachment($this->directoryRead->readFile($path . $filename), $type == null ? \Laminas\Mime\Mime::TYPE_OCTETSTREAM : "text/" . $type, \Laminas\Mime\Mime::DISPOSITION_INLINE, \Laminas\Mime\Mime::ENCODING_BASE64, basename($filename));
        }
        return $mail->send();*/


        $parts = [];

        $html = new \Laminas\Mime\Part($message);
        $html->type = \Laminas\Mime\Mime::TYPE_HTML;
        $html->charset = 'utf-8';
        $html->encoding = \Laminas\Mime\Mime::ENCODING_QUOTEDPRINTABLE;

        $parts[] = $html;

        if (!is_array($filenames)) {
            $filenames = [$filenames];
        }

        foreach ($filenames as $filename) {
            $file = new \Laminas\Mime\Part($this->directoryRead->readFile($path . $filename));
            $file->type = 'application/octet-stream';
            $file->filename = $filename;
            $file->disposition = \Laminas\Mime\Mime::DISPOSITION_ATTACHMENT;
            $file->encoding = \Laminas\Mime\Mime::ENCODING_BASE64;
            $parts[] = $file;
        }

        $body = new \Laminas\Mime\Message();
        $body->setParts($parts);

        $message = new \Laminas\Mail\Message();
        $message->setBody($body);
        $message->addFrom($mailFrom);
        $message->addTo($mailto);
        $message->addReplyTo($mailFrom);
        $message->setSubject($subject);
        $message->setEncoding('UTF-8');

        $contentTypeHeader = $message->getHeaders()->get('Content-Type');
        $contentTypeHeader->setType('multipart/related');


//        $transport = new \Laminas\Mail\Transport\Smtp();
        /*
        $options   = new \Laminas\Mail\Transport\SmtpOptions([
            'name' => 'localhost',
            'host' => '127.0.0.1',
            'port' => 25,
        ]);
        */
/*
        $options   = new \Laminas\Mail\Transport\SmtpOptions([
            'name'              => 'mail.sigmalatex.nl',
            'host'              => 'mail.sigmalatex.nl',
            'port'              => '587',
            'connection_class'  => 'login',
            'connection_config' => [
                'username' => 'test@sigmalatex.nl',
                'password' => 'LeKVMTz2NmAFMrkQW7H9',
            ],
        ]);
*/
//        $transport->setOptions($options);

        $transport = new \Laminas\Mail\Transport\Sendmail();

        return  $transport->send($message);



    }
}