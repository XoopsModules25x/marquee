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

// Script to list recent posts from Newbb 1 & 2
function b_marquee_newbb($limit, $dateformat, $itemssize)
{
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
    $block = array();

    $module_handler = xoops_getHandler('module');
    $newbb          = $module_handler->getByDirname('newbb');
    $newbb_version  = (int)$newbb->getInfo('version');

    if ($newbb_version >= 2) {
        $order          = 't.topic_time';
        $forum_handler  = xoops_getModuleHandler('forum', 'newbb');
        $module_handler = xoops_getHandler('module');
        $newbb          = $module_handler->getByDirname('newbb');

        if (null === $newbbConfig) {
            $config_handler = xoops_getHandler('config');
            $newbbConfig    = &$config_handler->getConfigsByCat(0, $newbb->getVar('mid'));
        }

        if (null === $access_forums) {
            $access_forums = $forum_handler->getForums(0, 'access'); // get all accessible forums
        }
        $valid_forums   = array_keys($access_forums);
        $forum_criteria = ' AND t.forum_id IN (' . implode(',', $valid_forums) . ')';
        unset($access_forums);
        $approve_criteria = ' AND t.approved = 1 AND p.approved = 1';
        $db               = XoopsDatabaseFactory::getDatabaseConnection();
        $query            = 'SELECT t.*, f.forum_name, f.allow_subject_prefix, p.post_id, p.icon, p.uid, p.poster_name, u.uname, u.name FROM ' . $db->prefix('bb_topics') . ' t, ' . $db->prefix('bb_forums') . ' f, ' . $db->prefix('bb_posts') . ' p LEFT JOIN ' . $db->prefix('users') . ' u ON u.uid = p.uid WHERE f.forum_id=t.forum_id ' . $forum_criteria . $approve_criteria . ' AND t.topic_last_post_id=p.post_id ORDER BY ' . $order . ' DESC';
        $result           = $db->query($query, $limit, 0);
        if (!$result) {
            return '';
        }
        $rows = array();
        while ($row = $db->fetchArray($result)) {
            $rows[] = $row;
        }
        if (count($rows) < 1) {
            return false;
        }
        $myts = MyTextSanitizer::getInstance();

        foreach ($rows as $arr) {
            $title = $myts->htmlSpecialChars($arr['topic_title']);
            if ($itemssize > 0) {
                $title = xoops_substr($title, 0, $itemssize + 3);
            }

            $block[] = array(
                'date'     => formatTimestamp($arr['topic_time'], $dateformat),
                'category' => $arr['forum_name'],
                'author'   => $arr['uid'],
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/newbb/viewtopic.php?topic_id=' . $arr['topic_id'] . '&amp;post_id=' . $arr['post_id'] . '#forumpost' . $arr['post_id'] . "'>" . $title . '</a>');
        }
    } else { // Newbb 1
        $db    = XoopsDatabaseFactory::getDatabaseConnection();
        $myts  = MyTextSanitizer::getInstance();
        $order = 't.topic_time';
        $query = 'SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.topic_time, t.topic_views, t.topic_replies, t.forum_id, f.forum_name FROM ' . $db->prefix('bb_topics') . ' t, ' . $db->prefix('bb_forums') . ' f WHERE f.forum_id=t.forum_id AND f.forum_type <> 1 ORDER BY ' . $order . ' DESC';
        if (!$result = $db->query($query, $limit, 0)) {
            return '';
        }
        while ($arr = $db->fetchArray($result)) {
            $lastpostername = $db->query('SELECT post_id, uid FROM ' . $db->prefix('bb_posts') . ' WHERE post_id = ' . $arr['topic_last_post_id']);
            while ($tmpdb = $db->fetchArray($lastpostername)) {
                $tmpuser = XoopsUser::getUnameFromId($tmpdb['uid']);
                $time    = formatTimestamp($arr['topic_time'], $dateformat);
            }
            $title = $myts->htmlSpecialChars($arr['topic_title']);
            if ($itemssize > 0) {
                $title = xoops_substr($title, 0, $itemssize + 3);
            }

            $block[] = array(
                'date'     => $time,
                'category' => $arr['forum_name'],
                'author'   => $tmpuser,
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/newbb/viewtopic.php?topic_id=' . $arr['topic_id'] . '&amp;forum=' . $arr['forum_id'] . '&amp;post_id=' . $arr['topic_last_post_id'] . '#forumpost' . $arr['topic_last_post_id'] . "'>" . $title . '</a>');
        }
    }

    return $block;
}
