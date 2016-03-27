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

//  ------------------------------------------------------------------------ //
//  TplLeagueStats plugin for Marquee 2.4                                    //
//  written by Defkon1 [defkon1 at gmail dot com]                            //
//  ------------------------------------------------------------------------ //

function b_marquee_tplleaguestats($limit, $dateformat, $itemssize)
{
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';

    //######################## SETTINGS ######################
    $display_season_name      = false; // display season name?
    $hour                     = 1; // GMT+1  -> var = 1
    $use_itemsize             = false; // use marquee $itemsize value?
    $overwrite_limit_settings = true; // overwrite marquee's limit settings?
    $new_limit                = 6; // new limit (valid only if
    //     overwrite_limit_settings = true)
    $overwrite_dateformat_settings = true; // overwrite marquee's dateformat?
    $new_dateformat                = 'd/m/Y'; // new dateformat (valid only if
    //     overwrite_dateformat_settings=true)
    //######################## SETTINGS ######################

    global $xoopsDB;

    if ($overwrite_limit_settings) {
        $limit = $new_limit;
    }
    if ($overwrite_dateformat_settings) {
        $dateformat = $new_dateformat;
    }

    $block  = array();
    $myts   = MyTextSanitizer::getInstance();
    $sql    = 'SELECT H.OpponentName as home, A.OpponentName as away, M.LeagueMatchHomeGoals as home_p, M.LeagueMatchAwayGoals as away_p,
                  M.LeagueMatchDate as date, S.SeasonName as season
           FROM ' . $xoopsDB->prefix('tplls_leaguematches') . ' M
           LEFT JOIN ' . $xoopsDB->prefix('tplls_opponents') . ' AS H ON M.LeagueMatchHomeID = H.OpponentID
           LEFT JOIN ' . $xoopsDB->prefix('tplls_opponents') . ' AS A ON M.LeagueMatchAwayID = A.OpponentID
           LEFT JOIN ' . $xoopsDB->prefix('tplls_seasonnames') . " AS S ON M.LeagueMatchSeasonID = S.SeasonID
           ORDER BY M.LeagueMatchDate DESC
           LIMIT 0,$limit";
    $result = $xoopsDB->query($sql);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $title = $myts->htmlSpecialChars($myrow['home']) . ' - ' . $myts->htmlSpecialChars($myrow['away']) . ' ' . $myts->htmlSpecialChars($myrow['home_p']) . '-' . $myts->htmlSpecialChars($myrow['away_p']);

        if ($use_itemsize && $itemssize > 0) {            
                $title = xoops_substr($title, 0, $itemssize + 3);          
        }

        $arr_date = explode('-', $myrow['date']);

        $season = '';

        if ($display_season_name) {
            $season = $myrow['season'];
        }

        $block[] = array(
            'date'     => formatTimestamp(mktime($hour, 0, 0, $arr_date[1], $arr_date[2], $arr_date[0]), $dateformat),
            'category' => $season,
            'author'   => '',
            'title'    => $title,
            'link'     => "<a href=\"" . XOOPS_URL . "/modules/tplleaguestats\">" . $title . '</a>');
    }

    return $block;
}
