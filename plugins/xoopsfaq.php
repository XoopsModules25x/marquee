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
 * @copyright          Hervé Thouzard (http://www.herve-thouzard.com)
 * @license            http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package            marquee
 * @author             Hervé Thouzard (http://www.herve-thouzard.com)
 * @version            $Id $
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateformat
 * @param $itemssize
 *
 * @return array
 */

// Script to list the recent links from the mylinks module version 1.10
function b_marquee_xoopsfaq($limit, $dateformat, $itemssize)
{
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
    $block  = array();
    $myts   = MyTextSanitizer::getInstance();
    $db     = XoopsDatabaseFactory::getDatabaseConnection();
    $result = $db->query('SELECT c.*, t.category_title FROM ' . $db->prefix('xoopsfaq_contents') . ' c, ' . $db->prefix('xoopsfaq_categories') . ' t WHERE c.contents_visible>0 AND (c. category_id=t.category_id) ORDER BY contents_time DESC', $limit, 0);
    while ($myrow = $db->fetchArray($result)) {
        $title = $myts->htmlSpecialChars($myrow['contents_title']);
        if ($itemssize > 0) {
            $title = xoops_substr($title, 0, $itemssize + 3);
        }
        $block[] = array(
            'date'     => formatTimestamp($myrow['contents_time'], $dateformat),
            'category' => $myts->htmlSpecialChars($myrow['category_title']),
            'author'   => 0,
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/xoopsfaq/index.php?cat_id=' . $myrow['category_id'] . '#q' . $myrow['contents_id'] . "'>" . $title . '</a>');
    }

    return $block;
}
