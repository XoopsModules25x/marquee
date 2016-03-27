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
 * @param $itemsize
 *
 * @return array
 */

//  ------------------------------------------------------------------------ //
//  Article plugin for Marquee 2.4                                           //
//  written by Defkon1 [defkon1 at gmail dot com]                            //
//  ------------------------------------------------------------------------ //

function b_marquee_article($limit, $dateformat, $itemsize)
{
    global $xoopsDB;
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
    require_once(XOOPS_ROOT_PATH . '/modules/article/include/functions.php');
    $block = array();
    $myts  = MyTextSanitizer::getInstance();

    static $access_cats;

    $artConfig = art_load_config();
    art_define_url_delimiter();

    $select   = 'art_id';
    $disp_tag = '';
    $from     = '';
    $where    = '';
    $order    = 'art_time_publish DESC';

    $select .= ', cat_id, art_title, uid, art_time_publish';

    if (null === $access_cats) {
        $permission_handler = xoops_getModuleHandler('permission', 'article');
        $access_cats        =& $permission_handler->getCategories('access');
    }
    $allowed_cats = $access_cats;

    $query = "SELECT $select FROM " . art_DB_prefix('article') . $from;
    $query .= ' WHERE cat_id IN (' . implode(',', $allowed_cats) . ') AND art_time_publish >0 ' . $where;
    $query .= ' ORDER BY ' . $order;
    $query .= ' LIMIT 0, ' . $limit;
    if (!$result = $xoopsDB->query($query)) {
        return false;
    }
    $rows   = array();
    $author = array();
    while ($row = $xoopsDB->fetchArray($result)) {
        $rows[]              = $row;
        $author[$row['uid']] = 1;
    }
    if (count($rows) < 1) {
        return false;
    }
    $author_name = XoopsUser::getUnameFromId(array_keys($author));

    $arts            = array();
    $uids            = array();
    $cids            = array();
    $article_handler = xoops_getModuleHandler('article', 'article');
    foreach ($rows as $row) {
        $article = $article_handler->create(false);
        $article->assignVars($row);
        $_art = array();
        foreach ($row as $tag => $val) {
            $_art[$tag] = @$article->getVar($tag);
        }
        $_art['author'] = $author_name[$row['uid']];

        $_art['date'] = $article->getTime($dateformat);

        $titlelength   = $itemsize + 3;
        $_art['title'] = xoops_substr($_art['art_title'], 0, $titlelength);

        $_art['category'] = '';

        $delimiter    = '/';
        $_art['link'] = "<a href=\"" . XOOPS_URL . "modules/article/view.article.php$delimiter" . $_art['art_id'] . '/c' . $_art['cat_id'] . "\"><strong>" . $_art['art_title'] . '</strong></a>';

        $arts[] = $_art;
        unset($article, $_art);
        $cids[$row['cat_id']] = 1;
    }

    $block = $arts;

    return $block;
}
