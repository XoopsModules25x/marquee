<?php
/**
 * ****************************************************************************
 * Marquee - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright          Hervé Thouzard (http://www.herve-thouzard.com)
 * @license            http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package            marquee
 * @author             Hervé Thouzard (http://www.herve-thouzard.com)
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

// Script to list the recent polls from the xoopspoll module version 1.0
function b_marquee_xoopspoll($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    $block  = [];
    $myts   = \MyTextSanitizer::getInstance();
    $db     = \XoopsDatabaseFactory::getDatabaseConnection();
    $result = $db->query('SELECT * FROM ' . $db->prefix('xoopspoll_desc') . ' WHERE start_time<=' . time() . ' AND end_time>' . time() . ' ORDER BY start_time DESC', $limit, 0);
    while (false !== ($myrow = $db->fetchArray($result))) {
        $title = $myts->htmlSpecialChars($myrow['question']);
        if ($itemsSize > 0) {
            $title = xoops_substr($title, 0, $itemsSize + 3);
        }
        $block[] = [
            'date'     => formatTimestamp($myrow['start_time'], $dateFormat),
            'category' => '',
            'author'   => $myrow['user_id'],
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/xoopspoll/index.php?poll_id=' . $myrow['poll_id'] . "'>" . $title . '</a>'
        ];
    }

    return $block;
}
