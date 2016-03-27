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
 * Version : $Id:
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateformat
 * @param $itemssize
 *
 * @return array
 */

// Script to list recent clips from the smartmedia module (tested with smartmedia 0.85)
function b_marquee_smartmedia($limit, $dateformat, $itemssize)
{
    $block = array();
    if (!defined('SMARTMEDIA_DIRNAME')) {
        define('SMARTMEDIA_DIRNAME', 'smartmedia');
    }
    include_once(XOOPS_ROOT_PATH . '/modules/' . SMARTMEDIA_DIRNAME . '/include/common.php');
    $title_length = 99999;
    if ($itemssize > 0) {
        $title_length = $itemssize;
    }
    $max_clips = $limit;

    $clipsArray =& $smartmedia_clip_handler->getClipsFromAdmin(0, $max_clips, 'clips.created_date', 'DESC', 'all');

    if ($clipsArray) {
        foreach ($clipsArray as $clipArray) {
            $clip    = array();
            $block[] = array(
                'date'     => '',
                'category' => '',
                'author'   => '',
                'title'    => $clipArray['title'],
                'link'     => '<a href="' . SMARTMEDIA_URL . 'clip.php?categoryid=' . $clipArray['categoryid'] . '&folderid=' . $clipArray['folderid'] . '&clipid=' . $clipArray['clipid'] . '">' . $clipArray['title'] . '</a>');
            unset($clip);
        }
    }

    return $block;
}
