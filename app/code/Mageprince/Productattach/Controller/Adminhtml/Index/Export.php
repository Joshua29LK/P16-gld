<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Mageprince\Productattach\Model\Productattach;

class Export extends Action
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Productattach
     */
    protected $attachmentModel;

    /**
     * Export constructor.
     *
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Productattach $attachmentModel
     * @throws FileSystemException
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Productattach $attachmentModel
    ) {
        parent::__construct($context);
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->attachmentModel = $attachmentModel;
    }

    /**
     * Export attachments csv
     *
     * @return ResponseInterface|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $name = date('m_d_Y_H_i_s');
        $filepath = 'export/custom' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $columns = $this->getColumnHeader();
        foreach ($columns as $column) {
            $header[] = $column;
        }
        $stream->writeCsv($header);

        $attachmentCollection = $this->attachmentModel->getCollection();

        foreach ($attachmentCollection as $key => $attachment) {
            $itemData = [];
            $itemData[] = $attachment->getId();
            $itemData[] = $attachment->getName();
            $itemData[] = $attachment->getDescription();
            $itemData[] = $attachment->getFile();
            $itemData[] = $attachment->getFileExt();
            $itemData[] = $attachment->getUrl();
            $itemData[] = $attachment->getStore();
            $itemData[] = $attachment->getCustomerGroup();
            $itemData[] = $attachment->getActive();
            $stream->writeCsv($itemData);
        }

        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '1';
        $csvFilename = 'attachments.csv';
        return $this->fileFactory->create($csvFilename, $content, DirectoryList::VAR_DIR);
    }

    /**
     * Header Columns
     * @return array
     */
    public function getColumnHeader()
    {
        return [
            'productattach_id',
            'name',
            'description',
            'file',
            'file_ext',
            'url',
            'store',
            'customer_group',
            'active'
        ];
    }
}
