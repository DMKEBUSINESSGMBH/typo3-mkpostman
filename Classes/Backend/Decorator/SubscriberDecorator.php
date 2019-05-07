<?php
namespace DMK\Mkpostman\Backend\Decorator;

use DMK\Mkpostman\Factory;

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

\tx_rnbase::load('Tx_Rnbase_Backend_Decorator_BaseDecorator');

/**
 * Subscriber lister
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class SubscriberDecorator
    extends \Tx_Rnbase_Backend_Decorator_BaseDecorator
{
    /**
     * Wraps the Value.
     * A childclass can extend this and wrap each value in a spac.
     * For example a strikethrough for disabled entries.
     *
     * @param string $formatedValue
     * @param \Tx_Rnbase_Domain_Model_DataInterface $entry
     * @param string $columnName
     *
     * @return string
     */
    protected function wrapValue(
        $formatedValue,
        \Tx_Rnbase_Domain_Model_DataInterface $entry,
        $columnName
    ) {
        return sprintf(
            '<span style="color:%3$s">%2$s</span>',
            CRLF,
            $formatedValue,
            $entry->getDisabled() ? '#600' : '#060'
        );
    }

    /**
     * Renders the useractions
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return string
     */
    protected function formatActionsColumn(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    ) {
        \tx_rnbase::load('tx_rnbase_util_TCA');

        $return = '';

        $tableName = $item->getTableName();
        // we use the real uid, not the uid of the parent!
        $uid = $item->getProperty('uid');

        $return .= $this->getFormTool()->createEditLink(
            $tableName,
            $uid,
            ''
        );

        $return .= $this->getFormTool()->createHideLink(
            $tableName,
            $uid,
            $item->getDisabled()
        );


        $return .= $this->getLogAction($item);

        $return .= $this->getFormTool()->createDeleteLink(
            $tableName,
            $uid,
            '',
            array(
                'confirm' => '###LABEL_SUBSCRIBER_DELETE_CONFIRM###'

            )
        );

        return $return;
    }

    /**
     * Renders the label column.
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return string
     */
    protected function formatEmailColumn(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    ) {

        $lastModifyDateTime = $item->getLastModifyDateTime();
        $creationDateTime = $item->getCreationDateTime();

        return sprintf(
            '<span title="UID: %3$d %1$sCreation: %4$s %1$sLast Change: %5$s">%2$s</span>',
            CRLF,
            $item->getEmail(),
            $item->getProperty('uid'),
            $creationDateTime ? $creationDateTime->format(\DateTime::ATOM) : '-',
            $lastModifyDateTime ? $lastModifyDateTime->format(\DateTime::ATOM) : '-'
        );
    }
    /**
     * Renders the label column.
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return string
     */
    protected function formatNameColumn(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    ) {
        $title = array_filter(
            array(
                \tx_rnbase_util_Lang::sL(
                    'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:' .
                        'tx_mkpostman_subscribers.gender.' . (int) $item->getGender()
                ),
                $item->getFirstName(),
                $item->getLastName()
            )
        );



        if (count($title) > 1) {
            $title = implode(' ', $title);
        } else {
            $title = 'unknown';
        }

        return $title;
    }

    /**
     * Renders the label column.
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return string
     */
    protected function formatCategoriesColumn(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    ) {
        $titles = [];
        if ((int)$item->getCategories() > 0) {
            $catRepo = Factory::getCategoryRepository();
            foreach ($catRepo->findBySubscriberId($item->getUid()) as $category) {
                $titles[] = $category->getTitle();
            }
        }

        return implode(',', $titles);
    }

    /**
     * Add a rudimentary log icon with only a tooltip
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return string
     */
    protected function getLogAction(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    )
    {
        $logs = $this->getLogs($item);
        $logToolTip = [];
        /* @var $log \DMK\Mkpostman\Domain\Model\LogModel */
        foreach ($logs as $log)
        {
            $logToolTip[] = $log->getCreationDateTime()->format('Y-m-d H:i:s') . ': ' . $log->getDescription();
        }

        if (count($logToolTip) > 15) {
            $logToolTip = array_merge(
                array_slice($logToolTip, 0, 5),
                ['...'],
                array_slice($logToolTip, -9)
            );
        }

        return sprintf(
            '<a href="#" class="btn btn-default btn-sm" title="%2$s">%1$s</a>',
            \Tx_Rnbase_Backend_Utility_Icons::getSpriteIcon('tcarecords-tx_mkpostman_logs-default'),
            count($logs) . ' Log(s): ' . CRLF . implode(CRLF, $logToolTip)
        );
    }

    /**
     * Returns a log collection for the subscribers.
     *
     * @param \Tx_Rnbase_Domain_Model_DataInterface $item
     *
     * @return null|\Tx_Rnbase_Domain_Model_DomainInterface
     */
    protected function getLogs(
        \Tx_Rnbase_Domain_Model_DataInterface $item
    )
    {
        return \DMK\Mkpostman\Factory::getLogRepository()->findBySubscriber($item);
    }
}
