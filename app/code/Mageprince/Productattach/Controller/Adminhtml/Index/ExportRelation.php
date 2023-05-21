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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;

class ExportRelation extends Export
{
    /**
     * Export attachments products relations csv
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

            $products = $this->attachmentModel->getProductsByAttachmentId($attachment->getId());

            if (count($products)) {
                $productStr = implode('|', $products);
                $itemData[] = $productStr;
            } else {
                $itemData[] = '';
            }

            $stream->writeCsv($itemData);
        }

        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '1';
        $csvFilename = 'attachments_relation.csv';
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
            'products'
        ];
    }
}
