<?php

namespace DMK\Mkpostman\Domain\Model;

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

use DMK\Mkpostman\Factory;

\tx_rnbase::load('Tx_Rnbase_Domain_Model_Base');

/**
 * Log Model.
 *
 * @method int                         getState()
 * @method Tx_Rnbase_Domain_Model_Data setState() setState(int $state)
 * @method int                         getDescription()
 * @method Tx_Rnbase_Domain_Model_Data setDescription() setDescription(string $uid)
 * @method int                         getCruserId()
 * @method Tx_Rnbase_Domain_Model_Data setCruserId() setCruserId(int $uid)
 * @method int                         getSubscriberId()
 * @method Tx_Rnbase_Domain_Model_Data setSubscriberId() setSubscriberId(int $uid)
 * @method int                         getDeleted()
 * @method Tx_Rnbase_Domain_Model_Data setDeleted() setDeleted(int $deleted)
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogModel extends \Tx_Rnbase_Domain_Model_Base
{
    use \Tx_Rnbase_Domain_Model_StorageTrait;

    const STATE_SUBSCRIBED = 1;
    const STATE_ACTIVATED = 2;
    const STATE_UNSUBSCRIBED = 3;

    /**
     * Retruns the tablename of the log.
     *
     * @return string Tabellenname als String
     */
    public function getTableName()
    {
        return 'tx_mkpostman_logs';
    }

    /**
     * Returns the subscriber for the log.
     *
     * @return SubscriberModel|null
     */
    public function getSubscriber()
    {
        if (!$this->getStorage()->hasSubscriber() && $this->getSubscriberId() > 0) {
            $this->getStorage()->setSubscriber(
                Factory::getSubscriberRepository()->findByUid($this->getSubscriberId())
            );
        }

        return $this->getStorage()->getSubscriber();
    }

    /**
     * Sets a subscriber for a log.
     *
     * @param SubscriberModel $subscriber
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setSubscriber(
        SubscriberModel $subscriber
    ) {
        if (!$subscriber->isPersisted()) {
            throw new \Exception('The subscriber has to ber persisted to append a log');
        }

        $this->setSubscriberId($subscriber->getUid());
        $this->getStorage()->setSubscriber($subscriber);

        return $this;
    }
}
