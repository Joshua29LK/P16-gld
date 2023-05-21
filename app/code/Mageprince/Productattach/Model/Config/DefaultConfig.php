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

namespace Mageprince\Productattach\Model\Config;

class DefaultConfig
{
    /**
     * Path to store config where count of attachment posts per page is stored
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE = 'productattach/view/items_per_page';

    /**
     * Media path to extension images
     * @var string
     */
    const MEDIA_PATH = 'productattach';

    /**
     * Default link icon
     */
    const LINK_ICON_PATH = 'Mageprince_Productattach::images/link.svg';

    /**
     * Default unknown file icon
     */
    const UNKNOWN_ICON_PATH = 'Mageprince_Productattach::images/unknown.svg';

    /**
     * Enable module config path
     */
    const XML_PATH_ENABLE = 'productattach/general/enable';

    /**
     * Attachment view config path
     */
    const XML_PATH_ATTACHMENT_VIEW = 'productattach/display/attachment_view';

    /**
     * Tab name config path
     */
    const XML_PATH_TABNAME = 'productattach/display/tabname';

    /**
     * Attachment list view type
     */
    const ATTACHMENT_VIEW_TYPE_LIST = 'list';

    /**
     * Attachment list view type
     */
    const XML_PATH_LIST_VIEW_COLUMNS = 'productattach/display/list_view_columns';

    /**
     * Attachment view type table
     */
    const ATTACHMENT_VIEW_TYPE_TABLE = 'table';

    /**
     * Attachment list view type
     */
    const XML_PATH_TABLE_VIEW_COLUMNS = 'productattach/display/table_view_columns';

    /**
     * Attachment icon column
     */
    const ICON_COLUMN = 'icon';

    /**
     * Attachment label column
     */
    const LABEL_COLUMN = 'label';

    /**
     * Attachment description column
     */
    const DESCRIPTION_COLUMN = 'description';

    /**
     * Attachment size column
     */
    const SIZE_COLUMN = 'size';

    /**
     * Attachment type column
     */
    const TYPE_COLUMN = 'type';

    /**
     * Attachment download column
     */
    const DOWNLOAD_COLUMN = 'download';

    /**
     * Attachment header column
     */
    const HEADER_COLUMN = 'header';

    /**
     * Unknown file icon path
     */
    const UNKNOWN_IMAGE_PATH = 'Mageprince_Productattach::images/unknown.png';

    /**
     * Default file icon
     * @return array
     */
    public function getDefaultIcons()
    {
        return [
            'csv',
            'doc',
            'docx',
            'download',
            'gif',
            'jpeg',
            'jpg',
            'link',
            'mov',
            'pdf',
            'png',
            'tgz',
            'txt',
            'zip'
        ];
    }

    /**
     * Allowed extension
     * @return array
     */
    public function getAllowedExt()
    {
        return [
            'pdf',
            'pptx',
            'xls',
            'xlsx',
            'flash',
            'mp3',
            'docx',
            'doc',
            'zip',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'ini',
            'readme',
            'avi',
            'csv',
            'txt',
            'wma',
            'mpg',
            'flv',
            'mp4'
        ];
    }
}
