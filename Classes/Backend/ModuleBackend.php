<?php
namespace DMK\Mkpostman\Backend;

/***************************************************************
 * Copyright notice
 *
 * (c) 2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

\tx_rnbase::load('tx_rnbase_mod_BaseModule');

/**
 * MK Postman backend module
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class ModuleBackend
    extends \tx_rnbase_mod_BaseModule
{
    /**
     * Initializes the backend module by setting internal variables, initializing the menu.
     *
     * @return void
     */
    public function init()
    {
        $GLOBALS['LANG']->includeLLFile('EXT:mkpostman/Resources/Private/Language/Backend.xlf');
        parent::init();
    }

    /**
     * Method to get the extension key
     *
     * @return string Extension key
     */
    public function getExtensionKey()
    {
        return 'mkpostman';
    }

    /**
     * Generates the module content.
     *
     * @return string
     */
    protected function moduleContent()
    {
        return $this->checkPid() ?: parent::moduleContent();
    }

    /**
     * Check for records on current pid and show list with pages with records
     *
     * @return mixed null or string
     */
    public function checkPid()
    {
        $pages = self::getStorageFolders();

        // check for records on the current page
        if ((empty($pages) || isset($pages[$this->getPid()]))) {
            return null;
        }

        // otherwise show a linklist
        foreach ($pages as $pid => &$page) {
            $pageinfo = \Tx_Rnbase_Backend_Utility::readPageAccess(
                $pid,
                $this->perms_clause
            );
            $modUrl = \Tx_Rnbase_Backend_Utility::getModuleUrl(
                'web_MkpostmanBackend',
                array('id' => $pid),
                ''
            );
            $page  = '<a href="' . $modUrl . '">';
            if ($pid === 0) {
                $page .= \Tx_Rnbase_Backend_Utility_Icons::getSpriteIcon(
                    'apps-pagetree-root',
                    array('size' => 'small')
                );
                $page .= ' ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
            } else {
                $page .= \Tx_Rnbase_Backend_Utility_Icons::getSpriteIconForRecord(
                    'pages',
                    \Tx_Rnbase_Database_Connection::getInstance()->getRecord('pages', $pid),
                    'small'
                );
                $page .= ' ' . $pageinfo['title'];
            }
            $page .= ' ' . htmlspecialchars($pageinfo['_thePath']);
            $page .= '</a>';

            $pages[$pid] = $page;
        }

        $out  = '<div class="tables graybox">';
        $out .= '<h2 class="bgColor2 t3-row-header">###LABEL_NO_PAGE_SELECTED###</h2>';
        if (!empty($pages)) {
            $out .= '<ul><li>' . implode('</li><li>', $pages) . '</li></ul>';
        }
        $out .= '</div>';

        return $out;
    }
    /**
     * Liefert Page Ids zu seiten mit mkpostman inhalten.
     * @return array
     */
    private static function getStorageFolders()
    {
        static $pids = false;
        if (is_array($pids)) {
            return $pids;
        }

        $repo = \DMK\Mkpostman\Factory::getSubscriberRepository();

        $pages = array_merge(
            // get all pageids
            \tx_rnbase_util_DB::doSelect(
                'pid as pageid',
                $repo->getEmptyModel()->getTableName(),
                array('enablefieldsoff' => 1)
            )
        );

        if (empty($pages)) {
            return array();
        }

        // merge the pages together
        $pages = call_user_func_array(
            'array_merge_recursive',
            array_values($pages)
        );

        if (empty($pages['pageid'])) {
            return array();
        }
        // Check for existing entry merges
        if (!is_array($pages['pageid'])) {
            $pages['pageid'] = array($pages['pageid']);
        }

        // convert the pids to keys
        $pages = array_flip($pages['pageid']);

        $pids = $pages;

        return $pids;
    }
}
