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

// Script to list the recent faqs from the xfaq module version 1.01
function b_marquee_xfaq($limit, $dateformat, $itemssize)
{
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
    $block  = array();
    $myts   = MyTextSanitizer::getInstance();
    $db     = XoopsDatabaseFactory::getDatabaseConnection();
    $result = $db->query('SELECT f.*, t.topic_title, t.topic_submitter FROM ' . $db->prefix('xfaq_faq') . ' f, ' . $db->prefix('xfaq_topic') . ' t WHERE f.faq_online>0 AND (f.faq_topic=t.topic_id) ORDER BY faq_date_created DESC', $limit, 0);
    while ($myrow = $db->fetchArray($result)) {
        $title = $myts->htmlSpecialChars($myrow['faq_question']);
        if ($itemssize > 0) {
            $title = xoops_substr($title, 0, $itemssize + 3);
        }
        $block[] = array(
            'date'     => formatTimestamp($myrow['faq_date_created'], $dateformat),
            'category' => $myts->htmlSpecialChars($myrow['topic_title']),
            'author'   => XoopsUser::getUnameFromId((int)$myrow['topic_submitter']),
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/xfaq/faq.php?faq_id=' . $myrow['faq_id'] . "'>{$title}</a>");
    }

    return $block;
}
