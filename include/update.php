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
 * @param XoopsModule|\XoopsObject $module
 * @param null                    $oldversion
 * @return mixed
 */

//function xoops_module_update_marquee()
//{
//    $db = \XoopsDatabaseFactory::getDatabaseConnection();
//    $sql = "ALTER TABLE `" . $db->prefix('marquee') . "` MODIFY `marquee_bgcolor` varchar(7) NOT NULL default '';";
//    $db->query($sql);
//
//    return true;
//}

function xoops_module_update_marquee(\XoopsObject $module, $oldversion = null)
{
    $db  = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'ALTER TABLE `' . $db->prefix('marquee') . "` MODIFY `marquee_bgcolor` VARCHAR(7) NOT NULL DEFAULT '';";
    $db->query($sql);

    if ($oldversion < 250) {

        // delete old block html template files
        $templateDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/templates/blocks/';
        $template_list     = array_diff(scandir($templateDirectory, SCANDIR_SORT_NONE), ['..', '.']);
        foreach ($template_list as $k => $v) {
            $fileinfo = new \SplFileInfo($templateDirectory . $v);
            if ('html' === $fileinfo->getExtension() && 'index.html' !== $fileinfo->getFilename()) {
                // @unlink($templateDirectory . $v);
                if (false === @unlink($templateDirectory . $v)) {
                    throw new \RuntimeException('The file ' . $templateDirectory . $v . ' could not be deleted.');
                }
            }
        }
        // Load class XoopsFile
        xoops_load('xoopsfile');
        //delete /images directory
        $imagesDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/images/';
        $folderHandler   = XoopsFile::getHandler('folder', $imagesDirectory);
        $folderHandler->delete($imagesDirectory);
        //delete /js directory
        $jsDirectory   = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/js/';
        $folderHandler = XoopsFile::getHandler('folder', $jsDirectory);
        $folderHandler->delete($jsDirectory);
        //delete /templates/style.css file
        $deleteFile    = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/admin/marquee.php';
        $folderHandler = XoopsFile::getHandler('file', $deleteFile);
        $folderHandler->delete($deleteFile);
    }

    $gpermHandler = xoops_getHandler('groupperm');

    return $gpermHandler->deleteByModule($module->getVar('mid'), 'item_read');
}
