<?php
/**
 * ****************************************************************************
 * marquee - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright         Hervé Thouzard (http://www.herve-thouzard.com)
 * @license           http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package           marquee
 * @author            Hervé Thouzard (http://www.herve-thouzard.com)
 *
 * Version :
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

// Script to list recent clips from the smartmedia module (tested with smartmedia 0.85)
function b_marquee_smartmedia($limit, $dateFormat, $itemsSize)
{
    $block = [];
    if (!defined('SMARTMEDIA_DIRNAME')) {
        define('SMARTMEDIA_DIRNAME', 'smartmedia');
    }
    require_once XOOPS_ROOT_PATH . '/modules/' . SMARTMEDIA_DIRNAME . '/include/common.php';
    $title_length = 99999;
    if ($itemsSize > 0) {
        $title_length = $itemsSize;
    }
    $maxClips = $limit;

    $smartmediaClipHandler = smartmedia_gethandler('clip');

    $clipsArray =& $smartmediaClipHandler->getClipsFromAdmin(0, $maxClips, 'clips.created_date', 'DESC', 'all');

    if ($clipsArray) {
        foreach ($clipsArray as $clipArray) {
            $clip    = [];
            $block[] = [
                'date'     => '',
                'category' => '',
                'author'   => '',
                'title'    => $clipArray['title'],
                'link'     => '<a href="' . SMARTMEDIA_URL . 'clip.php?categoryid=' . $clipArray['categoryid'] . '&folderid=' . $clipArray['folderid'] . '&clipid=' . $clipArray['clipid'] . '">' . $clipArray['title'] . '</a>'
            ];
            unset($clip);
        }
    }

    return $block;
}
