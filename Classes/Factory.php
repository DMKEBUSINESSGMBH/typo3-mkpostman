<?php
namespace DMK\Mkpostman;

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

/**
 * MK Postman Factory
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
final class Factory
{
    /**
     * Returns a storage
     *
     * @return \Tx_Rnbase_Domain_Model_Data
     */
    protected static function getStorage()
    {
        static $storage = null;

        if ($storage === null) {
            $storage = \tx_rnbase::makeInstance(
                'Tx_Rnbase_Domain_Model_Data'
            );
        }

        return $storage;
    }

    /**
     * Returns a cache
     *
     * @return \tx_rnbase_cache_ICache
     */
    public static function getCache()
    {
        \tx_rnbase::load('tx_rnbase_cache_Manager');

        return \tx_rnbase_cache_Manager::getCache('mkpostman');
    }

    /**
     * Returns the subscriber repository
     *
     * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
     */
    public static function getSubscriberRepository()
    {
        return \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository'
        );
    }

    /**
     * Creates the mail processor
     *
     * @param \tx_rnbase_configurations $configurations
     *
     * @return \DMK\Mkpostman\Mail\ProcessorMail
     */
    public static function getProcessorMail(
        \tx_rnbase_configurations $configurations
    ) {
        return \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Mail\\ProcessorMail',
            $configurations
        );
    }

    /**
     * Creates mail receiver
     *
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
     *
     * @return \DMK\Mkpostman\Mail\Receiver\SubscriberReceiver
     */
    public static function getSubscriberMailReceiver(
        \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
    ) {
        return \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Mail\\Receiver\\SubscriberReceiver',
            $subscriber
        );
    }

    /**
     * Creates an double opt in util instance with an subscriber
     *
     * @param string|DMK\Mkpostman\Domain\Model\SubscriberModel $subscriberOrActivationKey
     *
     * @return \DMK\Mkpostman\Utility\DoubleOptInUtility
     */
    public static function getDoubleOptInUtility(
        $subscriberOrActivationKey
    ) {
        return \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            $subscriberOrActivationKey
        );
    }

    /**
     * Creates the crypt utility
     *
     * @return \DMK\Mkpostman\Utility\CryptUtility
     */
    public static function getCryptUtility()
    {
        return \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Utility\\CryptUtility'
        );
    }
}
