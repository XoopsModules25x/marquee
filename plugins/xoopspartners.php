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

// Script to list recent partners from the xoopspartners module (tested with version 1.1)
function b_marquee_xoopspartners($limit, $dateformat, $itemssize)
{
    $block    = array();
    $myts     = MyTextSanitizer::getInstance();
    $arrayIds = array();
    $arrayIds = xoopspartners_random($limit);
    global $xoopsDB;

    foreach ($arrayIds as $id) {
        $result = $xoopsDB->query('SELECT id, url, image, title FROM ' . $xoopsDB->prefix('partners') . " WHERE id=$id");
        list($id, $url, $image, $title) = $xoopsDB->fetchrow($result);
        $origtitle = $title;
        $title     = $myts->htmlSpecialChars($title);
        if ($itemssize > 0) {
            $title = $myts->htmlSpecialChars(substr($origtitle, 0, 19));
        } else {
            $title = $myts->htmlSpecialChars($origtitle);
        }

        $block[] = array(
            'date'     => '',
            'category' => '',
            'author'   => '',
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/xoopspartners/vpartner.php?id=' . $id . "'>" . $title . '</a>');
    }

    return $block;
}

/**
 * @param        $NumberPartners
 * @param bool   $random
 * @param string $orden
 * @param string $desc
 *
 * @return array
 */
function xoopspartners_random($NumberPartners, $random = true, $orden = '', $desc = '')
{
    global $xoopsDB;
    $PartnersId  = array();
    $ArrayReturn = array();
    if ($random) {
        $result  = $xoopsDB->query('SELECT id FROM ' . $xoopsDB->prefix('partners') . ' WHERE status = 1');
        $numrows = $xoopsDB->getRowsNum($result);
    } else {
        $result = $xoopsDB->query('SELECT id FROM ' . $xoopsDB->prefix('partners') . ' Where status = 1 ORDER BY ' . $orden . ' ' . $desc, $NumberPartners);
    }
    while ($ret = $xoopsDB->fetchArray($result)) {
        $PartnersId[] = $ret['id'];
    }
    if (($numrows <= $NumberPartners) || (!$random)) {
        return $PartnersId;
        //        exit();
    }
    $NumberTotal  = 0;
    $TotalPartner = count($PartnersId) - 1;
    while ($NumberPartners > $NumberTotal) {
        $RandomPart = mt_rand(0, $TotalPartner);
        if (!in_array($PartnersId[$RandomPart], $ArrayReturn)) {
            $ArrayReturn[] = $PartnersId[$RandomPart];
            ++$NumberTotal;
        }
    }

    return $ArrayReturn;
}
