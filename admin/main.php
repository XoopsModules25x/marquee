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
 *
 * ****************************************************************************
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Marquee;

require_once __DIR__ . '/admin_header.php';
require \dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/marquee/admin/functions.php';
//require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
//require_once XOOPS_ROOT_PATH . '/modules/marquee/class/marquee_utils.php';
$adminObject = Admin::getInstance();
$op          = Request::getString('op', Request::getCmd('op', 'default', 'POST'), 'GET');
// Verify that a field exists inside a mysql table
/**
 * @param $fieldname
 * @param $table
 *
 * @return bool
 */
function marquee_FieldExists($fieldname, $table)
{
    global $xoopsDB;
    $result = $xoopsDB->queryF("SHOW COLUMNS FROM   $table LIKE '$fieldname'");
    return ($xoopsDB->getRowsNum($result) > 0);
}

// Verify if the table is up to date
if (!marquee_FieldExists('marquee_marqueeid', $xoopsDB->prefix('marquee'))) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . ' CHANGE `marqueeid` `marquee_marqueeid` INT( 8 ) NOT NULL AUTO_INCREMENT');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `uid` `marquee_uid` MEDIUMINT( 8 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `direction` `marquee_direction` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `scrollamount` `marquee_scrollamount` INT( 11 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `behaviour` `marquee_behaviour` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . ' CHANGE `bgcolor` `marquee_bgcolor` VARCHAR( 7 ) NOT NULL');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `align` `marquee_align` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `height` `marquee_height` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . ' CHANGE `width` `marquee_width` VARCHAR( 4 ) NOT NULL');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `hspace` `marquee_hspace` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `scrolldelay` `marquee_scrolldelay` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `stoponmouseover` `marquee_stoponmouseover` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `loop` `marquee_loop` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `vspace` `marquee_vspace` SMALLINT( 6 ) NOT NULL DEFAULT '0'");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . ' CHANGE `content` `marquee_content` TEXT NOT NULL');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('marquee') . " CHANGE `source` `marquee_source` VARCHAR( 255 ) NOT NULL DEFAULT 'fixed'");
}
//$marqueeHandler = Marquee\Helper::getInstance()->getHandler('Marqueex');
//$marqueeHandler = new MarqueeHandler($db);
// Function used to add and modify an element
/**
 * @param        $marqueeid
 * @param        $Action
 * @param        $FormTitle
 * @param        $contentvalue
 * @param        $bgcolorvalue
 * @param        $widthvalue
 * @param        $heightvalue
 * @param        $scrollamountvalue
 * @param        $hspacevalue
 * @param        $vspacevalue
 * @param        $scrolldelayvalue
 * @param        $directionvalue
 * @param        $behaviourvalue
 * @param        $alignvalue
 * @param        $loopvalue
 * @param        $stopvalue
 * @param        $LabelSubmitButton
 * @param string $sourcevalue
 * @throws \Exception
 */
function AddEditMarqueeForm(
    $marqueeid,
    $Action,
    $FormTitle,
    $contentvalue,
    $bgcolorvalue,
    $widthvalue,
    $heightvalue,
    $scrollamountvalue,
    $hspacevalue,
    $vspacevalue,
    $scrolldelayvalue,
    $directionvalue,
    $behaviourvalue,
    $alignvalue,
    $loopvalue,
    $stopvalue,
    $LabelSubmitButton,
    $sourcevalue = 'fixed'
) {
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    global $xoopsModule;
    $sform  = new \XoopsThemeForm($FormTitle, 'marqueeform', XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/main.php');
    $source = new \XoopsFormSelect(_AM_MARQUEE_SOURCE, 'source', $sourcevalue);
    $source->addOption('fixed', _AM_MARQUEE_SOURCE_FIXED);
    $fileslst = myglob(XOOPS_ROOT_PATH . '/modules/marquee/plugins/', 'php');
    foreach ($fileslst as $onefile) {
        $onefile = basename($onefile, '.php');
        $source->addOption($onefile, $onefile);
    }
    $sform->addElement($source);
    $utility = new Marquee\Utility();
    $editor  = $utility::getWysiwygForm(_AM_MARQUEE_CONTENT, 'content', $contentvalue, 15, 60, 'content_text_hidden');
    if ($editor) {
        $sform->addElement($editor, false);
    }
    if ('DHTML' !== $utility::getModuleOption('methodtouse')) {
        // $sform->addElement(new \XoopsFormText(_AM_MARQUEE_BGCOLOR, 'bgcolor', 7, 7, $bgcolorvalue), false);
        $sform->addElement(new \XoopsFormColorPicker(_AM_MARQUEE_BGCOLOR, 'bgcolor', $bgcolorvalue), false);
    }
    $sform->addElement(new \XoopsFormText(_AM_MARQUEE_WIDTH, 'width', 4, 4, $widthvalue), false);
    $sform->addElement(new \XoopsFormText(_AM_MARQUEE_HEIGHT, 'height', 4, 4, $heightvalue), false);
    $sform->addElement(new \XoopsFormText(_AM_MARQUEE_SCRAMOUNT, 'scrollamount', 4, 4, $scrollamountvalue), false);
    if ('DHTML' !== $utility::getModuleOption('methodtouse')) {
        $sform->addElement(new \XoopsFormText(_AM_MARQUEE_HSPACE, 'hspace', 4, 4, $hspacevalue), false);
        $sform->addElement(new \XoopsFormText(_AM_MARQUEE_VSPACE, 'vspace', 4, 4, $vspacevalue), false);
    }
    $sform->addElement(new \XoopsFormText(_AM_MARQUEE_SCRDELAY, 'scrolldelay', 6, 6, $scrolldelayvalue), false);
    $direction = new \XoopsFormSelect(_AM_MARQUEE_DIRECTION, 'direction', $directionvalue);
    $direction->addOption('0', _AM_MARQUEE_DIRECTION1);
    $direction->addOption('1', _AM_MARQUEE_DIRECTION2);
    $direction->addOption('2', _AM_MARQUEE_DIRECTION3);
    $direction->addOption('3', _AM_MARQUEE_DIRECTION4);
    $sform->addElement($direction, true);
    $behaviour = new \XoopsFormSelect(_AM_MARQUEE_BEHAVIOUR, 'behaviour', $behaviourvalue);
    $behaviour->addOption('0', _AM_MARQUEE_BEHAVIOUR1);
    if ('DHTML' !== $utility::getModuleOption('methodtouse')) {
        $behaviour->addOption('1', _AM_MARQUEE_BEHAVIOUR2);
    }
    $behaviour->addOption('2', _AM_MARQUEE_BEHAVIOUR3);
    $sform->addElement($behaviour, true);
    if ('DHTML' !== $utility::getModuleOption('methodtouse')) {
        $align = new \XoopsFormSelect(_AM_MARQUEE_ALIGN, 'align', $alignvalue);
        $align->addOption('0', _AM_MARQUEE_ALIGN1);
        $align->addOption('1', _AM_MARQUEE_ALIGN2);
        $align->addOption('2', _AM_MARQUEE_ALIGN3);
        $sform->addElement($align, true);
    }
    $loop = new \XoopsFormSelect(_AM_MARQUEE_LOOP, 'loop', $loopvalue);
    $loop->addOption('0', _AM_MARQUEE_INFINITELOOP);
    for ($i = 1; $i <= 100; ++$i) {
        $loop->addOption($i, $i);
    }
    if ('DHTML' !== $utility::getModuleOption('methodtouse')) {
        $sform->addElement($loop, true);
        $sform->addElement(new \XoopsFormRadioYN(_AM_MARQUEE_STOP, 'stoponmouseover', $stopvalue, _YES, _NO));
    }
    $sform->addElement(new \XoopsFormHidden('op', $Action), false);
    if (!empty($marqueeid)) {
        $sform->addElement(new \XoopsFormHidden('marqueeid', $marqueeid), false);
    }
    $buttonTray = new \XoopsFormElementTray('', '');
    $submit_btn = new \XoopsFormButton('', 'submit', $LabelSubmitButton, 'submit');
    $buttonTray->addElement($submit_btn);
    $cancel_btn = new \XoopsFormButton('', 'reset', _AM_MARQUEE_RESETBUTTON, 'reset');
    $buttonTray->addElement($cancel_btn);
    $sform->addElement($buttonTray);
    $sform->display();
}

// ******************************************************************************************************************************************
// **** Main ********************************************************************************************************************************
// ******************************************************************************************************************************************
switch ($op) {
    // Verify before to edit an element
    case 'verifybeforeedit':
        if ('' !== Request::getString('submit', '', 'POST')) {
            $marquee = $marqueeHandler->get(Request::getInt('marqueeid', 0, 'POST'));
            if (is_object($marquee)) {
                $marquee->setVar('marquee_uid', $xoopsUser->getVar('uid'));
                $marquee->setVar('marquee_direction', Request::getString('direction', '', 'POST'));
                $marquee->setVar('marquee_scrollamount', Request::getInt('scrollamount', 0, 'POST'));
                $marquee->setVar('marquee_behaviour', Request::getInt('behaviour', 0, 'POST'));
                $marquee->setVar('marquee_bgcolor', Request::getString('bgcolor', '', 'POST'));
                $marquee->setVar('marquee_align', Request::getInt('align', 0, 'POST'));
                $marquee->setVar('marquee_height', Request::getInt('height', 0, 'POST'));
                $marquee->setVar('marquee_width', Request::getString('width', '', 'POST'));
                $marquee->setVar('marquee_hspace', Request::getInt('hspace', 0, 'POST'));
                $marquee->setVar('marquee_scrolldelay', Request::getInt('scrolldelay', 0, 'POST'));
                $marquee->setVar('marquee_stoponmouseover', Request::getInt('stoponmouseover', 0, 'POST'));
                $marquee->setVar('marquee_loop', Request::getInt('loop', 0, 'POST'));
                $marquee->setVar('marquee_vspace', Request::getInt('vspace', 0, 'POST'));
                $marquee->setVar('marquee_content', Request::getText('content', '', 'POST'));
                $marquee->setVar('marquee_source', Request::getString('source', '', 'POST'));
                if (!$marqueeHandler->insert($marquee)) {
                    redirect_header('main.php', 1, _AM_MARQUEE_ERROR_MODIFY_DB);
                }
                redirect_header('main.php', 1, _AM_MARQUEE_DBUPDATED);
            } else {
                redirect_header('main.php', 3, _ERRORS);
            }
        }
        break;
    // Edit an element
    case 'edit':
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        echo '<br>';
        if (\Xmf\Request::hasVar('marqueeid', 'GET')) {
            $marqueeid = Request::getInt('marqueeid', 0, 'GET');
            $marquee   = $marqueeHandler->get($marqueeid);
            AddEditMarqueeForm(
                $marqueeid,
                'verifybeforeedit',
                _AM_MARQUEE_CONFIG,
                $marquee->getVar('marquee_content', 'e'),
                $marquee->getVar('marquee_bgcolor', 'e'),
                $marquee->getVar('marquee_width', 'e'),
                $marquee->getVar('marquee_height', 'e'),
                $marquee->getVar('marquee_scrollamount', 'e'),
                $marquee->getVar('marquee_hspace', 'e'),
                $marquee->getVar('marquee_vspace', 'e'),
                $marquee->getVar('marquee_scrolldelay', 'e'),
                $marquee->getVar('marquee_direction', 'e'),
                $marquee->getVar('marquee_behaviour', 'e'),
                $marquee->getVar('marquee_align', 'e'),
                $marquee->getVar('marquee_loop', 'e'),
                $marquee->getVar('marquee_stoponmouseover', 'e'),
                _AM_MARQUEE_UPDATE,
                $marquee->getVar('marquee_source', 'e')
            );
        }
        break;
    // Delete an element
    case 'delete':
        if ('' !== Request::getString('ok', '', 'POST')) {
            xoops_cp_header();
            $adminObject->displayNavigation(basename(__FILE__));
            // echo '<h4>' . _AM_MARQUEE_CONFIG . '</h4>';
            xoops_confirm(
                [
                    'op'        => 'delete',
                    'marqueeid' => Request::getInt('marqueeid', 0, 'GET'),
                    'ok'        => 1,
                ],
                'main.php',
                _AM_MARQUEE_RUSUREDEL
            );
        } else {
            if (empty($_POST['marqueeid'])) {
                redirect_header('main.php', 2, _AM_MARQUEE_ERROR_ADD_MARQUEE);
            }
            $marqueeid = Request::getInt('marqueeid', 0, 'POST');
            $marquee   = $marqueeHandler->deleteAll(new \Criteria('marquee_marqueeid', $marqueeid, '='));
            redirect_header('main.php', 1, _AM_MARQUEE_DBUPDATED);
        }
        break;
    // Verify before to add an element
    case 'verifytoadd':
        if ('' !== Request::getString('submit', '', 'POST')) {
            $vres = $marqueeHandler->quickInsert(
                [
                    'marquee_uid'             => $xoopsUser->getVar('uid'),
                    'marquee_direction'       => Request::getString('direction', '', 'POST'),
                    'marquee_scrollamount'    => Request::getInt('scrollamount', 0, 'POST'),
                    'marquee_behaviour'       => Request::getInt('behaviour', 0, 'POST'),
                    'marquee_bgcolor'         => Request::getString('bgcolor', '', 'POST'),
                    'marquee_align'           => Request::getInt('align', 0, 'POST'),
                    'marquee_height'          => Request::getInt('height', 0, 'POST'),
                    'marquee_width'           => Request::getString('width', '', 'POST'),
                    'marquee_hspace'          => Request::getInt('hspace', 0, 'POST'),
                    'marquee_scrolldelay'     => Request::getInt('scrolldelay', 0, 'POST'),
                    'marquee_stoponmouseover' => Request::getInt('stoponmouseover', 0, 'POST'),
                    'marquee_loop'            => Request::getInt('loop', 0, 'POST'),
                    'marquee_vspace'          => Request::getInt('vspace', 0, 'POST'),
                    'marquee_content'         => Request::getText('content', '', 'POST'),
                    'marquee_source'          => Request::getString('source', '', 'POST'),
                ]
            );
            if (!$vres) {
                redirect_header('main.php', 1, _AM_MARQUEE_ERROR_ADD_MARQUEE);
            }
            redirect_header('main.php', 1, _AM_MARQUEE_ADDED_OK);
        }
        break;
    // Display the form to add an element
    case 'addmarquee':
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        echo '<br>';
        try {
            AddEditMarqueeForm(0, 'verifytoadd', _AM_MARQUEE_CONFIG, '', '', '', '', '', 0, 0, '', 0, 0, 0, 0, 0, _AM_MARQUEE_ADDBUTTON, 'fixed');
        } catch (\Exception $e) {
        }
        break;
    // Default action, list all elements
    case 'default':
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        //        echo '<h4>' . _AM_MARQUEE_CONFIG . "</h4><br>\n";
        echo "<table width='100%' border='0' cellspacing='1' class='outer'>\n";
        echo "<tr><th align='center'>"
             . _AM_MARQUEE_ID
             . "</th><th align='center'>"
             . _AM_MARQUEE_CONTENT
             . "</th><th align='center'>"
             . _AM_MARQUEE_BGCOLOR_SHORT
             . "</th><th align='center'>"
             . _AM_MARQUEE_BEHAVIOUR
             . "</th><th align='center'>"
             . _AM_MARQUEE_SOURCE
             . "</th><th align='center'>"
             . _AM_MARQUEE_STOP
             . "</th><th align='center'>"
             . _AM_MARQUEE_DIRECTION
             . "</th><th align='center'>"
             . _AM_MARQUEE_ACTION
             . "</th></tr>\n";
        $marqueeArray = $marqueeHandler->getObjects();
        $class        = 'even';
        $baseurl      = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/main.php';
        $tbldirection = [
            _AM_MARQUEE_DIRECTION1,
            _AM_MARQUEE_DIRECTION2,
            _AM_MARQUEE_DIRECTION3,
            _AM_MARQUEE_DIRECTION4,
        ];
        $tblbehaviour = [_AM_MARQUEE_BEHAVIOUR1, _AM_MARQUEE_BEHAVIOUR2, _AM_MARQUEE_BEHAVIOUR3];
        if (count($marqueeArray) > 0) {
            /** @var Marquee\Marqueex $marquee */
            foreach ($marqueeArray as $marquee) {
                //              $action_edit="<a href='".$baseurl."?op=edit&marqueeid=".$marquee->getVar('marquee_marqueeid')."'>"._AM_MARQUEE_EDIT."</a>";
                //              $action_delete="<a href='".$baseurl."?op=delete&marqueeid=".$marquee->getVar('marquee_marqueeid')."'>"._AM_MARQUEE_DELETE."</a>";
                $action_edit   = '<a href=' . $baseurl . '?op=edit&marqueeid=' . $marquee->getVar('marquee_marqueeid') . '><img src=' . $pathIcon16 . '/edit.png title=' . _AM_MARQUEE_EDIT . '></a>';
                $action_delete = '<a href=' . $baseurl . '?op=delete&marqueeid=' . $marquee->getVar('marquee_marqueeid') . '><img src=' . $pathIcon16 . '/delete.png title=' . _AM_MARQUEE_DELETE . '></a>';
                $bgcolorvalue  = $marquee->getVar('marquee_bgcolor');
                $direction     = $tbldirection[$marquee->getVar('marquee_direction')];
                $behaviour     = $tblbehaviour[$marquee->getVar('marquee_behaviour')];
                $stop          = _YES;
                if (0 == $marquee->getVar('marquee_stoponmouseover')) {
                    $stop = _NO;
                }
                $source = $marquee->getVar('marquee_source');
                if ('fixed' === $marquee->getVar('marquee_source')) {
                    $source = _AM_MARQUEE_SOURCE_FIXED;
                }
                echo "<tr class='"
                     . $class
                     . "'><td align='center'>"
                     . $marquee->getVar('marquee_marqueeid')
                     . "</td><td align='left'>"
                     . Marquee\Utility::truncateHtml($marquee->getVar('marquee_content'), 80, '...', false, true)
                     . "</td><td align='center'>"
                     . "<div style='height:12px; width:12px; background-color:"
                     . $bgcolorvalue
                     . "; border:1px solid black;float:left; margin-right:5px;'></div>"
                     . $bgcolorvalue
                     . "</td><td align='center'>"
                     . $behaviour
                     . "</td><td align='center'>"
                     . $source
                     . "</td><td align='center'>"
                     . $stop
                     . "</td><td align='center'>"
                     . $direction
                     . "</td><td align='center'>"
                     . $action_edit
                     . '&nbsp;&nbsp;'
                     . $action_delete
                     . "</td></tr>\n";
                $class = ('even' === $class) ? 'odd' : 'even';
            }
        }
        //      echo "<tr class='".$class."'><td colspan='7' align='center'><form name='faddmarquee' method='post' action='main.php'><input type='hidden' name='op' value='addmarquee'><input type='submit' name='submit' value='"._AM_MARQUEE_ADDMARQUEE."'></td></tr>";
        $adminObject->addItemButton(_AM_MARQUEE_ADDMARQUEE, 'main.php?op=addmarquee', 'add', '');
        $adminObject->displayButton('left', '');
        echo '</table>';
        break;
}
require_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
