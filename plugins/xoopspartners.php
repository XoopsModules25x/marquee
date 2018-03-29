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
 * Version :
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

// Script to list recent partners from the xoopspartners module (tested with version 1.1)
function b_marquee_xoopspartners($limit, $dateFormat, $itemsSize)
{
    $block    = [];
    $myts     = \MyTextSanitizer::getInstance();
    $arrayIds = [];
    $arrayIds = xoopspartners_random($limit);
    global $xoopsDB;

    foreach ($arrayIds as $id) {
        $result = $xoopsDB->query('SELECT id, url, image, title FROM ' . $xoopsDB->prefix('partners') . " WHERE id=$id");
        list($id, $url, $image, $title) = $xoopsDB->fetchRow($result);
        $origtitle = $title;
        $title     = $myts->htmlSpecialChars($title);
        if ($itemsSize > 0) {
            $title = $myts->htmlSpecialChars(substr($origtitle, 0, 19));
        } else {
            $title = $myts->htmlSpecialChars($origtitle);
        }

        $block[] = [
            'date'     => '',
            'category' => '',
            'author'   => '',
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/xoopspartners/vpartner.php?id=' . $id . "'>" . $title . '</a>'
        ];
    }

    return $block;
}

/**
 * @param        $numberPartners
 * @param bool   $random
 * @param string $orden
 * @param string $desc
 *
 * @return array
 */
function xoopspartners_random($numberPartners, $random = true, $orden = '', $desc = '')
{
    global $xoopsDB;
    $PartnersId  = [];
    $ArrayReturn = [];
    $numrows     = 0;
    if ($random) {
        $result  = $xoopsDB->query('SELECT id FROM ' . $xoopsDB->prefix('partners') . ' WHERE status = 1');
        $numrows = $xoopsDB->getRowsNum($result);
    } else {
        $result = $xoopsDB->query('SELECT id FROM ' . $xoopsDB->prefix('partners') . ' WHERE status = 1 ORDER BY ' . $orden . ' ' . $desc, $numberPartners);
    }
    while (false !== ($ret = $xoopsDB->fetchArray($result))) {
        $PartnersId[] = $ret['id'];
    }
    if (($numrows <= $numberPartners) || (!$random)) {
        return $PartnersId;
        //        exit();
    }
    $numberTotal  = 0;
    $totalPartner = count($PartnersId) - 1;
    while ($numberPartners > $numberTotal) {
        $RandomPart = mt_rand(0, $totalPartner);
        if (!in_array($PartnersId[$RandomPart], $ArrayReturn)) {
            $ArrayReturn[] = $PartnersId[$RandomPart];
            ++$numberTotal;
        }
    }

    return $ArrayReturn;
}
