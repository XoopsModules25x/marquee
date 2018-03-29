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

//  ------------------------------------------------------------------------ //
//  Article plugin for Marquee 2.4                                           //
//  written by Defkon1 [defkon1 at gmail dot com]                            //
//  ------------------------------------------------------------------------ //

use XoopsModules\Article;

/**
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 * @return array|false
 */
function b_marquee_article($limit, $dateFormat, $itemsSize)
{
    global $xoopsDB;
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    require_once XOOPS_ROOT_PATH . '/modules/article/include/functions.php';
    $block = [];
    $myts  = \MyTextSanitizer::getInstance();

    static $accessCats;

    $artConfig = art_load_config();
    art_define_url_delimiter();

    $select  = 'art_id';
    $dispTag = '';
    $from    = '';
    $where   = '';
    $order   = 'art_time_publish DESC';

    $select .= ', cat_id, art_title, uid, art_time_publish';

    if (null === $accessCats) {
        $permissionHandler = Article\Helper::getInstance()->getHandler('Permission');
        $accessCats        = $permissionHandler->getCategories('access');
    }
    $allowedCats = $accessCats;

    $query = "SELECT $select FROM " . art_DB_prefix('article') . $from;
    $query .= ' WHERE cat_id IN (' . implode(',', $allowedCats) . ') AND art_time_publish >0 ' . $where;
    $query .= ' ORDER BY ' . $order;
    $query .= ' LIMIT 0, ' . $limit;
    if (!$result = $xoopsDB->query($query)) {
        return false;
    }
    $rows   = [];
    $author = [];
    while (false !== ($row = $xoopsDB->fetchArray($result))) {
        $rows[]              = $row;
        $author[$row['uid']] = 1;
    }
    if (count($rows) < 1) {
        return false;
    }
    $authorName = XoopsUser::getUnameFromId(array_keys($author));

    $arts           = [];
    $uids           = [];
    $cids           = [];
    $articleHandler = Article\Helper::getInstance()->getHandler('Article');
    foreach ($rows as $row) {
        $article = $articleHandler->create(false);
        $article->assignVars($row);
        $_art = [];
        foreach ($row as $tag => $val) {
            $_art[$tag] = @$article->getVar($tag);
        }
        $_art['author'] = $authorName[$row['uid']];

        $_art['date'] = $article->getTime($dateFormat);

        $titlelength   = $itemsSize + 3;
        $_art['title'] = xoops_substr($_art['art_title'], 0, $titlelength);

        $_art['category'] = '';

        $delimiter    = '/';
        $_art['link'] = '<a href="' . XOOPS_URL . "modules/article/view.article.php$delimiter" . $_art['art_id'] . '/c' . $_art['cat_id'] . '"><strong>' . $_art['art_title'] . '</strong></a>';

        $arts[] = $_art;
        unset($article, $_art);
        $cids[$row['cat_id']] = 1;
    }

    $block = $arts;

    return $block;
}
