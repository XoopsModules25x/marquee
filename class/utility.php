<?php

namespace XoopsModules\Marquee;

use XoopsModules\Marquee\{Common,
    Constants,
    Helper
};

/** @var Helper $helper */

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------
    public const MODULE_NAME = 'marquee';

    /**
     * Access the only instance of this class
     *
     * @return \XoopsModules\Marquee\Utility
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param string $caption Caption to give to the editor
     * @param string $name    Editor's name
     * @param string $value   Editor's value
     * @param string $width   Editor's width
     * @param string $height  Editor's height
     * @param string $supplemental
     *
     * @return \XoopsFormDhtmlTextArea|\XoopsFormEditor The editor to use
     */
    public static function getWysiwygForm(
        $caption,
        $name,
        $value = '',
        $width = '100%',
        $height = '400px',
        $supplemental = ''
    ) {
        $helper = Helper::getInstance();
        if (\class_exists('XoopsFormEditor')) {
            $options['name']   = $name;
            $options['value']  = $value;
            $options['rows']   = 35;
            $options['cols']   = '100%';
            $options['width']  = '100%';
            $options['height'] = '400px';
            $editor            = new \XoopsFormEditor($caption, $helper->getConfig('form_options'), $options, $nohtml = false, $onfailure = 'textarea');
        } else {
            $editor = new \XoopsFormDhtmlTextArea($caption, $name, $value, '100%', '100%');
        }
        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param string $message Message to display
     * @param bool   $form    Is this a confirmation for a form ?
     *
     * @return string the javascript code to insert in the link (or in the form)
     */
    public static function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . \str_replace("'", ' ', $message) . "')\"";
        }
        return "onSubmit=\"javascript:return confirm('" . \str_replace("'", ' ', $message) . "')\"";
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param mixed  $time
     */
    public static function redirect($message = '', $url = 'index.php', $time = 2)
    {
        \redirect_header($url, $time, $message);
    }

    /**
     * Internal function used to get the handler of the current module
     * @return \XoopsModule The module
     * @deprecated  use $helper->getModule();
     */
    protected static function getModule()
    {
        $moduleDirName = \basename(\dirname(__DIR__));
        static $mymodule;
        if (null === $mymodule) {
            global $xoopsModule;
            if (null !== $xoopsModule && \is_object($xoopsModule) && $moduleDirName == $xoopsModule->getVar('dirname')) {
                $mymodule = $xoopsModule;
            } else {
                $moduleHandler = \xoops_getHandler('module');
                $mymodule      = $moduleHandler->getByDirname($moduleDirName);
            }
        }
        return $mymodule;
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return bool Yes = we need to add them, false = no
     */
    public static function needsAsterisk()
    {
        if (false === mb_stripos(\XOOPS_VERSION, 'legacy')) {
            $xv = \xoops_trim(\str_replace('XOOPS ', '', \XOOPS_VERSION));
            if ((int)mb_substr($xv, 4, 2) >= 17) {
                return false;
            }
        }
        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param \XoopsThemeForm $sform The form to modify
     *
     * @return \XoopsThemeForm The modified form
     * @internal param string $caracter The character to use to mark fields
     */
    public static function formMarkRequiredFields($sform)
    {
        $required = $elements = [];
        if (self::needsAsterisk()) {
            foreach ($sform->getRequired() as $item) {
                $required[] = $item->_name;
            }
            $elements = &$sform->getElements();
            foreach ($elements as $i => $iValue) {
                if (\is_object($elements[$i]) && \in_array($iValue->_name, $required)) {
                    $iValue->_caption .= ' *';
                }
            }
        }
        return $sform;
    }

    /**
     * @param        $option
     * @param string $repmodule
     * @return bool
     */
    public static function getModuleOption($option, $repmodule = 'marquee')
    {
        global $xoopsModule;
        $helper = Helper::getInstance();
        static $tbloptions = [];
        if (\is_array($tbloptions) && \array_key_exists($option, $tbloptions)) {
            return $tbloptions[$option];
        }
        $retval = false;
        if (null !== $helper->getModule()
            && (\is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule
                && $xoopsModule->getVar('isactive'))) {
            if ('' !== $helper->getConfig($option)) {
                $retval = $helper->getConfig($option);
            }
        } else {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = \xoops_getHandler('module');
            $module        = $moduleHandler->getByDirname($repmodule);
            $configHandler = \xoops_getHandler('config');
            if ($module) {
                $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $tbloptions[$option] = $retval;
        return $retval;
    }

    /**
     * Verify if the current "user" is a bot or not
     *
     * If you have a problem with this function, insert the folowing code just before the line if (\Xmf\Request::hasVar('news_cache_bot', 'SESSION'))) { :
     * return false;
     *
     * @package          Marquee
     * @author           Hervé Thouzard (http://www.herve-thouzard.com)
     * @copyright    (c) Hervé Thouzard
     */
    public static function isBot()
    {
        if (\Xmf\Request::hasVar('marquee_cache_bot', 'SESSION')) {
            return $_SESSION['marquee_cache_bot'];
        }
        // Add here every bot you know separated by a pipe | (not matter with the upper or lower cases)
        // If you want to see the result for yourself, add your navigator's user agent at the end (mozilla for example)
        $botlist      = 'AbachoBOT|Arachnoidea|ASPSeek|Atomz|cosmos|crawl25-public.alexa.com|CrawlerBoy Pinpoint.com|Crawler|DeepIndex|EchO!|exabot|Excalibur Internet Spider|FAST-WebCrawler|Fluffy the spider|GAIS Robot/1.0B2|GaisLab data gatherer|Google|Googlebot-Image|googlebot|Gulliver|ia_archiver|Infoseek|Links2Go|Lycos_Spider_(modspider)|Lycos_Spider_(T-Rex)|MantraAgent|Mata Hari|Mercator|MicrosoftPrototypeCrawler|Mozilla@somewhere.com|MSNBOT|NEC Research Agent|NetMechanic|Nokia-WAPToolkit|nttdirectory_robot|Openfind|Oracle Ultra Search|PicoSearch|Pompos|Scooter|Slider_Search_v1-de|Slurp|Slurp.so|SlySearch|Spider|Spinne|SurferF3|Surfnomore Spider|suzuran|teomaagent1|TurnitinBot|Ultraseek|VoilaBot|vspider|W3C_Validator|Web Link Validator|WebTrends|WebZIP|whatUseek_winona|WISEbot|Xenu Link Sleuth|ZyBorg';
        $botlist      = mb_strtoupper($botlist);
        $currentagent = mb_strtoupper(\xoops_getenv('HTTP_USER_AGENT'));
        $retval       = false;
        $botarray     = \explode('|', $botlist);
        foreach ($botarray as $onebot) {
            if (false !== mb_strpos($currentagent, $onebot)) {
                $retval = true;
                break;
            }
        }
        $_SESSION['marquee_cache_bot'] = $retval;
        return $retval;
    }

    /**
     * Escape a string so that it can be included in a javascript string
     *
     * @param $string
     *
     * @return mixed
     */
    public static function javascriptEscape($string)
    {
        return \str_replace("'", "\\'", $string);
    }
}
