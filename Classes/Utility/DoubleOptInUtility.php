<?php
namespace DMK\Mkpostman\Utility;

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

use \DMK\Mkpostman\Domain\Model\SubscriberModel;

/**
 * MK Postman Double-Opt-In utility
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DoubleOptInUtility
{
    /**
     * The subscriber
     *
     * @var \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    private $subscriber = null;

    /**
     * The Constructor
     *
     * @param string|SubscriberModel $subscriberOrActivationKey
     *
     * @throws \BadMethodCallException
     */
    public function __construct(
        $subscriberOrActivationKey
    ) {
        // check for activatoin key
        if (is_string($subscriberOrActivationKey)) {
            $subscriberOrActivationKey = $this->findSubscriberByKey(
                $this->decodeActivationKey($subscriberOrActivationKey)
            );
        }

        if (!$subscriberOrActivationKey instanceof SubscriberModel) {
            throw new \BadMethodCallException(
                'No valid subscriber model given for double opt in',
                1464951846
            );
        }

        $this->subscriber = $subscriberOrActivationKey;
    }

    /**
     * Finds a subscriber by key
     *
     * @param \Tx_Rnbase_Domain_Model_Data $keyData
     *
     * @return null|SubscriberModel
     */
    protected function findSubscriberByKey(
        \Tx_Rnbase_Domain_Model_Data $keyData
    ) {
        if (!$keyData->getUid()) {
            return null;
        }

        return $this->getRepository()->findByUid($keyData->getUid());
    }

    /**
     * The current subscriber
     *
     * @return SubscriberModel
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Returns the subscriber repository
     *
     * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
     */
    protected function getRepository()
    {
        return \DMK\Mkpostman\Factory::getSubscriberRepository();
    }

    /**
     * Updates subscriber model with a new confirmstring
     * and persist the changes
     *
     * @return void
     */
    protected function updateConfirmString()
    {
        $confirmString = $this->createConfirmString();
        $this->getSubscriber()->setConfirmstring($confirmString);

        $this->getRepository()->persist($this->getSubscriber());
    }

    /**
     * Creates a new confirmstring for the subscriber
     *
     * @return string
     */
    protected function createConfirmString()
    {
        return md5(\uniqid($this->getSubscriber()->getUid()));
    }

    /**
     * Check if the activation key is valid for the current user
     *
     * @param string $activationKey
     *
     * @return bool
     */
    protected function validateActivationKey(
        $activationKey
    ) {
        $subscriber = $this->getSubscriber();

        $keyData = $this->decodeActivationKey($activationKey);

        return (
            $subscriber->getUid() == $keyData->getUid() &&
            $subscriber->getConfirmstring() === $keyData->getConfirmstring() &&
            md5($subscriber->getEmail()) === $keyData->getMailHash()
        );
    }

    /**
     * Decodes the activatoin key and extracts the informations
     *
     * @param string $activationKey
     *
     * @return Tx_Rnbase_Domain_Model_Data
     */
    protected function decodeActivationKey(
        $activationKey
    ) {
        // the key loks like base64 and urlencoded
        if (\substr_count($activationKey, ':') !== 2) {
            $crypt = \DMK\Mkpostman\Factory::getCryptUtility();
            $activationKey = $crypt->urlDencode($activationKey);
        }

        list ($uid, $confirmstring, $md5) = explode(':', $activationKey);

        return \Tx_Rnbase_Domain_Model_Data::getInstance(
            array(
                'uid' => $uid,
                'confirmstring' => $confirmstring,
                'mail_hash' => $md5
            )
        );
    }

    /**
     * Creates a activation key for the subscriber
     * uid:confirmstring:mailmd5
     *
     * @param bool $urlencode
     *
     * @return string
     */
    public function buildActivationKey(
        $urlencode = false
    ) {
        $subscriber = $this->getSubscriber();

        if (!$subscriber->isPersisted()) {
            throw new \Exception(
                'The subscriber has to ber persisted to create an activation key'
            );
        }
        if (!$subscriber->getEmail()) {
            throw new \Exception(
                'The subscriber neds a email to create an activation key'
            );
        }

        if (!$subscriber->getConfirmstring()) {
            $this->updateConfirmString();
        }

        $key = implode(
            ':',
            array(
                $subscriber->getUid(),
                $subscriber->getConfirmstring(),
                md5($subscriber->getEmail())
            )
        );

        // make the key base64 and url encoded
        if ($urlencode) {
            $crypt = \DMK\Mkpostman\Factory::getCryptUtility();
            $key = $crypt->urlEncode($key);
        }

        return $key;
    }

    /**
     * Validates the activation key and activates the subscriber
     *
     * @param string $activationKey
     *
     * @return bool
     */
    public function activateByKey(
        $activationKey
    ) {
        if (!$this->validateActivationKey($activationKey)) {
            return false;
        }

        $subscriber = $this->getSubscriber();
        $subscriber->setConfirmstring('');
        $subscriber->setDisabled(0);

        $this->getRepository()->persist($subscriber);

        return true;
    }
}
