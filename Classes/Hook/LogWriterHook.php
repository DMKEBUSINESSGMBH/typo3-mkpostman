<?php

namespace DMK\Mkpostman\Hook;

/***************************************************************
 * Copyright notice
 *
 * (c) 2018 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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

/**
 * MK Postman hook to add markers to direct_mail.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogWriterHook
{
    /**
     * @var array Of uids which are already proceddes/logged
     */
    protected $subscribersProcessed = [];

    /**
     * Hook to check if log relevant fields have changed.
     *
     * @param string                                   $status
     * @param string                                   $table
     * @param int                                      $uid
     * @param array                                    $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    public function processDatamap_postProcessFieldArray(
        $status, $table, $uid, $fieldArray, $dataHandler
    ) {
        // does nothing if no subscriber table was changed
        if ('tx_mkpostman_subscribers' != $table) {
            return;
        }

        // log only if the disabled or the email field was changed!
        if (!isset($fieldArray['disabled']) && !isset($fieldArray['email'])) {
            // set the subscriber as already processed! so it will not be processed again in afterAllOperations!
            $this->subscribersProcessed[$uid] = true;

            return;
        }
    }

    /**
     * Add subscriber log if a subscriber was edited.
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    public function processDatamap_afterAllOperations($dataHandler)
    {
        // Nothing to do?
        if (!is_object($dataHandler) || empty($dataHandler->datamap)) {
            return;
        }

        // the regular datamap structure
        // array(
        //     tablename => array(
        //         uid123 => array(),
        //     )
        // )

        // for history undo the structure looks likethis
        // array(
        //     tablename:uid123 => -1
        // )
        foreach ($dataHandler->datamap as $table => $uids) {
            // check the history undo structure and rebuild to reneral structure
            if (false !== strpos($table, ':')) {
                list($table, $uid) = explode(':', $table, 2);
                if ($uid > 0) {
                    $uids = [$uid => []];
                }
            }

            if (!is_array($uids) || 'tx_mkpostman_subscribers' !== $table) {
                continue;
            }

            foreach ($uids as $uid => $record) {
                // skip if the subscriber already was processed!
                if (!empty($this->subscribersProcessed[$uid])) {
                    continue;
                }
                $this->subscribersProcessed[$uid] = true;

                $new = false;
                // New element? get the uid from the new id map!
                if (!is_numeric($uid)) {
                    $uid = $dataHandler->substNEWwithIDs[$uid];
                    $new = true;
                }
                /*
                elseif (empty($uids[$uid]['disabled']) && empty(empty($uids[$uid]['email']))) {
                    // nothing to log. the state does not have changed
                    continue;
                }
                */

                $this->processSubscriberLog($uid, $new);
            }
        }
    }

    /**
     * Creates a log entry for a subscriber.
     *
     * @param $uid
     * @param bool $new
     */
    protected function processSubscriberLog($uid, $new = false)
    {
        $subscriber = $this->findSubscriberByUid($uid);
        if (null == $subscriber) {
            return;
        }
        $logManager = $this->getLogManager();

        // subscriber was just subscribed
        if ($new) {
            $logManager->createSubscribedBySubscriber($subscriber);
            // subscriber was created as enabled, just create another log
            if (!$subscriber->getDisabled()) {
                $logManager->createActivatedBySubscriber($subscriber);
            }
        }
        // subscriber was unsubscribed
        elseif ($subscriber->getDisabled()) {
            $logManager->createUnsubscribedBySubscriber($subscriber);
        }
        // subscriber is activated!
        else {
            $logManager->createActivatedBySubscriber($subscriber);
        }
    }

    /**
     * Returns a subscriber model by uid. Only a wrapper for unittests.
     *
     * @return \DMK\Mkpostman\Domain\Model\SubscriberModel|null
     */
    protected function findSubscriberByUid($uid)
    {
        return \DMK\Mkpostman\Factory::getSubscriberRepository()->findByUid($uid);
    }

    /**
     * Returns the log manager. Only a wrapper for unittests.
     *
     * @return \DMK\Mkpostman\Domain\Manager\LogManager
     */
    protected function getLogManager()
    {
        return \DMK\Mkpostman\Factory::getLogManager();
    }
}
