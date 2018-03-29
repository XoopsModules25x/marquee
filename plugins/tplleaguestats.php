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
//  TplLeagueStats plugin for Marquee 2.4                                    //
//  written by Defkon1 [defkon1 at gmail dot com]                            //
//  ------------------------------------------------------------------------ //

function b_marquee_tplleaguestats($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';

    //######################## SETTINGS ######################
    $displaySeason  = false; // display season name?
    $hour           = 1; // GMT+1  -> var = 1
    $useItemSize    = false; // use marquee $itemsize value?
    $overwriteLimit = true; // overwrite marquee's limit settings?
    $newLimit       = 6; // new limit (valid only if
    //     overwrite_limit_settings = true)
    $overwriteDateformat = true; // overwrite marquee's dateformat?
    $newDateformat       = 'd/m/Y'; // new dateformat (valid only if
    //     overwrite_dateformat_settings=true)
    //######################## SETTINGS ######################

    global $xoopsDB;

    if ($overwriteLimit) {
        $limit = $newLimit;
    }
    if ($overwriteDateformat) {
        $dateFormat = $newDateformat;
    }

    $block  = [];
    $myts   = \MyTextSanitizer::getInstance();
    $sql    = 'SELECT H.OpponentName as home, A.OpponentName as away, M.LeagueMatchHomeGoals as home_p, M.LeagueMatchAwayGoals as away_p,
                  M.LeagueMatchDate as date, S.SeasonName as season
           FROM ' . $xoopsDB->prefix('tplls_leaguematches') . ' M
           LEFT JOIN ' . $xoopsDB->prefix('tplls_opponents') . ' AS H ON M.LeagueMatchHomeID = H.OpponentID
           LEFT JOIN ' . $xoopsDB->prefix('tplls_opponents') . ' AS A ON M.LeagueMatchAwayID = A.OpponentID
           LEFT JOIN ' . $xoopsDB->prefix('tplls_seasonnames') . " AS S ON M.LeagueMatchSeasonID = S.SeasonID
           ORDER BY M.LeagueMatchDate DESC
           LIMIT 0,$limit";
    $result = $xoopsDB->query($sql);
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $title = $myts->htmlSpecialChars($myrow['home']) . ' - ' . $myts->htmlSpecialChars($myrow['away']) . ' ' . $myts->htmlSpecialChars($myrow['home_p']) . '-' . $myts->htmlSpecialChars($myrow['away_p']);

        if ($useItemSize && $itemsSize > 0) {
            $title = xoops_substr($title, 0, $itemsSize + 3);
        }

        $arrDate = explode('-', $myrow['date']);

        $season = '';

        if ($displaySeason) {
            $season = $myrow['season'];
        }

        $block[] = [
            'date'     => formatTimestamp(mktime($hour, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]), $dateFormat),
            'category' => $season,
            'author'   => '',
            'title'    => $title,
            'link'     => '<a href="' . XOOPS_URL . '/modules/tplleaguestats">' . $title . '</a>'
        ];
    }

    return $block;
}
