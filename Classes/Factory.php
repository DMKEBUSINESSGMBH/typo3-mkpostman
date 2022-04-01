<?php

namespace DMK\Mkpostman;

use Sys25\RnBase\Cache\CacheInterface;
use Sys25\RnBase\Cache\CacheManager;
use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Domain\Model\DataModel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * MK Postman Factory.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
final class Factory
{
    /**
     * Returns a storage.
     *
     * @return DataModel
     */
    private static function getStorage()
    {
        static $storage = null;

        if (null === $storage) {
            $storage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                DataModel::class
            );
        }

        return $storage;
    }

    /**
     * Returns a cache.
     *
     * @return CacheInterface
     */
    public static function getCache()
    {
        return CacheManager::getCache('mkpostman');
    }

    /**
     * Returns the subscriber repository.
     *
     * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
     */
    public static function getSubscriberRepository()
    {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository'
        );
    }

    /**
     * Returns the category repository.
     *
     * @return \DMK\Mkpostman\Domain\Repository\CategoryRepository
     */
    public static function getCategoryRepository()
    {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Domain\\Repository\\CategoryRepository'
        );
    }

    /**
     * Returns the log repository.
     *
     * @return \DMK\Mkpostman\Domain\Repository\LogRepository
     */
    public static function getLogRepository()
    {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Domain\\Repository\\LogRepository'
        );
    }

    /**
     * Returns the log manager.
     *
     * @return \DMK\Mkpostman\Domain\Manager\LogManager
     */
    public static function getLogManager()
    {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Domain\\Manager\\LogManager'
        );
    }

    /**
     * Creates the mail processor.
     *
     * @param Processor $configurations
     *
     * @return \DMK\Mkpostman\Mail\ProcessorMail
     */
    public static function getProcessorMail(
        Processor $configurations
    ) {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Mail\\ProcessorMail',
            $configurations
        );
    }

    /**
     * Creates mail receiver.
     *
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
     *
     * @return \DMK\Mkpostman\Mail\Receiver\SubscriberReceiver
     */
    public static function getSubscriberMailReceiver(
        Domain\Model\SubscriberModel $subscriber
    ) {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Mail\\Receiver\\SubscriberReceiver',
            $subscriber
        );
    }

    /**
     * Creates an double opt in util instance with an subscriber.
     *
     * @param string|DMK\Mkpostman\Domain\Model\SubscriberModel $subscriberOrActivationKey
     *
     * @return \DMK\Mkpostman\Utility\DoubleOptInUtility
     */
    public static function getDoubleOptInUtility(
        $subscriberOrActivationKey
    ) {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            $subscriberOrActivationKey
        );
    }

    /**
     * Creates the crypt utility.
     *
     * @return \DMK\Mkpostman\Utility\CryptUtility
     */
    public static function getCryptUtility()
    {
        return GeneralUtility::makeInstance(
            'DMK\\Mkpostman\\Utility\\CryptUtility'
        );
    }
}
