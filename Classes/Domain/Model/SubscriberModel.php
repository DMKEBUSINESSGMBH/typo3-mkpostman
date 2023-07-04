<?php

namespace DMK\Mkpostman\Domain\Model;

use Sys25\RnBase\Domain\Model\BaseModel;

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
 * Subscriber Model.
 *
 * @method int                         getGender()
 * @method \Sys25\RnBase\Domain\Model\DataModel setGender() setGender(int $gender)
 * @method bool                        hasGender()
 * @method \Sys25\RnBase\Domain\Model\DataModel unsGender()
 * @method string                      getFirstName()
 * @method \Sys25\RnBase\Domain\Model\DataModel setFirstName() setFirstName(string $firstname)
 * @method bool                        hasFirstName()
 * @method \Sys25\RnBase\Domain\Model\DataModel unsFirstName()
 * @method string                      getLastName()
 * @method \Sys25\RnBase\Domain\Model\DataModel setLastName() setLastName(string $lastname)
 * @method bool                        hasLastName()
 * @method \Sys25\RnBase\Domain\Model\DataModel unsLastName()
 * @method string                      getEmail()
 * @method \Sys25\RnBase\Domain\Model\DataModel setEmail() setEmail(string $email)
 * @method bool                        hasEmail()
 * @method \Sys25\RnBase\Domain\Model\DataModel unsEmail()
 * @method string                      getConfirmstring()
 * @method \Sys25\RnBase\Domain\Model\DataModel setConfirmstring() setConfirmstring(string $confirmstring)
 * @method bool                        hasConfirmstring()
 * @method \Sys25\RnBase\Domain\Model\DataModel unsConfirmstring()
 * @method int                         getDisabled()
 * @method \Sys25\RnBase\Domain\Model\DataModel setDisabled() setDisabled(int $disabled)
 * @method int                         getDeleted()
 * @method \Sys25\RnBase\Domain\Model\DataModel setDeleted() setDeleted(int $deleted)
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberModel extends BaseModel
{
    public function hasCategories()
    {
        return (int) $this->getCategories() > 0;
    }

    /**
     * Liefert den aktuellen Tabellenname.
     *
     * @return Tabellenname als String
     */
    public function getTableName()
    {
        return 'tx_mkpostman_subscribers';
    }
}
