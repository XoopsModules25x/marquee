<?php
/**
 * ****************************************************************************
 * marquee - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard (http://www.herve-thouzard.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Herv� Thouzard (http://www.herve-thouzard.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         marquee
 * @author 			Herv� Thouzard (http://www.herve-thouzard.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */

// Script to list recent files from the wfdownloads module (tested with wfdownloads 3.1)
function b_marquee_wfdownloads($limit, $dateformat, $itemssize)
{
	$block = array();

    global $xoopsUser;
    $modhandler = xoops_gethandler('module');
    $wfModule = $modhandler->getByDirname("wfdownloads");
    $config_handler = xoops_gethandler('config');
    $wfModuleConfig = $config_handler->getConfigsByCat(0, $wfModule->getVar('mid'));

    $groups = (is_object($xoopsUser)) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler = &xoops_gethandler('groupperm');
    $allowed_cats = $gperm_handler->getItemIds("WFDownCatPerm", $groups, $wfModule->getVar('mid'));

    $criteria = new Criteria("cid", "(".implode(',', $allowed_cats).")", "IN");
	$criteria = new CriteriaCompo(new Criteria('offline', 0));
    $criteria->setSort('published');
    $criteria->setOrder("DESC");
    $criteria->setLimit($limit);
    $download_handler = xoops_getmodulehandler('download', 'wfdownloads');
    $category_handler = xoops_getmodulehandler('category', 'wfdownloads');
    $buffer_category = array();

    $downloads = $download_handler->getObjects($criteria);

    foreach (array_keys($downloads) as $i) {
        $download = $downloads[$i]->toArray();
		if($itemssize > 0) {
			$title = xoops_substr($download['title'], 0, $itemssize);
		} else {
		    $title = $download['title'];
		}
		if(isset($buffer_category[$download['cid']])) {
			$categtitle = $buffer_category[$download['cid']];
		} else {
            $category = $category_handler->get($download['cid']);
            $categtitle = $buffer_category[$download['cid']] = $category->getVar('title');
		}
		$block[]=array(	'date'	=> formatTimestamp($download['published'], $wfModuleConfig['dateformat']),
						'category' => $categtitle,
						'author'=> $download['publisher'],
						'title'=> $title,
						'link' =>"<a href='".XOOPS_URL.'/modules/wfdownloads/singlefile.php?cid='.$download['cid'].'&lid='.$download['lid']."'>".$title.'</a>');
    }
	return $block;
}
?>
