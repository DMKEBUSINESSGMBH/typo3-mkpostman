<?php
namespace DMK\Mkpostman\Domain\Manager;

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

use DMK\Mkpostman\Domain\Model\LogModel;
use DMK\Mkpostman\Domain\Model\SubscriberModel;
use DMK\Mkpostman\Factory;

/**
 * Log repo
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogManager
{
    /**
     * Creates an subscibed log for the subscriber
     *
     * @param SubscriberModel $subscriber
     *
     * @return void
     */
    public function createSubscribedBySubscriber(
        SubscriberModel $subscriber
    ) {
        $this->createLogBySubscriber($subscriber, LogModel::STATE_SUBSCRIBED);
    }

    /**
     * Creates an activated log for the subscriber
     *
     * @param SubscriberModel $subscriber
     *
     * @return void
     */
    public function createActivatedBySubscriber(
        SubscriberModel $subscriber
    ) {
        $this->createLogBySubscriber($subscriber, LogModel::STATE_ACTIVATED);
    }

    /**
     * Creates an unsubscribed log for the subscriber
     *
     * @param SubscriberModel $subscriber
     *
     * @return void
     */
    public function createUnsubscribedBySubscriber(
        SubscriberModel $subscriber
    ) {
        $this->createLogBySubscriber($subscriber, LogModel::STATE_UNSUBSCRIBED);
    }

    /**
     * Creates an Log for the Subscriber
     *
     * @param SubscriberModel $subscriber
     * @param int $state LogModel::STATE_*
     *
     * @return void
     */
    protected function createLogBySubscriber(
        SubscriberModel $subscriber,
        $state
    ) {
        $repo = $this->getRepository();

        /* @var $logEntry \DMK\Mkpostman\Domain\Model\LogModel */
        $logEntry = $repo->createNewModel();

        if (TYPO3_MODE == 'BE') {
            $logEntry->setCruserId(\tx_rnbase_util_TYPO3::getBEUserUID());
        }

        $logEntry->setPid($subscriber->getPid());
        $logEntry->setSubscriber($subscriber);
        $logEntry->setState($state);
        $logEntry->setDescription($this->createDescription($logEntry));

        $repo->persist($logEntry);
    }

    /**
     * Creates the log description
     *
     * @param LogModel $logEntry
     *
     * @return string
     */
    protected function createDescription(
        LogModel $logEntry
    ) {
        // $log = '{subscriber/beuser} has {state} for {email}';
        $msg = '%1$s has %2$s for %3$s';

        $who = 'Subscriber';
        if ($logEntry->getCruserId() > 0) {
            $who = 'BE-User (' . $this->getBeUserName($logEntry->getCruserId()) . ')';
        }
        $what = $this->getStateLabel($logEntry->getState());
        $whom = $logEntry->getSubscriber()->getEmail();

        return sprintf($msg, $who, $what, $whom);
    }

    /**
     * Returns the Name of the User ID
     *
     * @param int $uid
     *
     * @return string
     */
    protected function getBeUserName(
        $uid
    ) {
        if ($uid == \tx_rnbase_util_TYPO3::getBEUserUID()) {
            $beuser = \tx_rnbase_util_TYPO3::getBEUser();

            return $beuser->user['username'];
        }

        $beuser = \Tx_Rnbase_Database_Connection::getInstance()->doSelect(
            'username',
            'be_users',
            ['where' => 'uid = ' . (int) $uid]
        );

        if (!empty($beuser)) {
            return $beuser[0]['username'];
        }

        return '';
    }

    /**
     * Converts the state id to a human readable label
     * @param int $state
     * @return string
     */
    protected function getStateLabel(
        $state
    ) {
        switch ($state) {
            case LogModel::STATE_SUBSCRIBED:
                return 'Subscribed';
            case LogModel::STATE_ACTIVATED:
                return 'Activated';
            case LogModel::STATE_UNSUBSCRIBED:
                return 'Unsubscribed';
        }

        return '';
    }

    /**
     * Returns the log repo
     *
     * @return \DMK\Mkpostman\Domain\Repository\LogRepository
     */
    protected function getRepository()
    {
        return Factory::getLogRepository();
    }
}
