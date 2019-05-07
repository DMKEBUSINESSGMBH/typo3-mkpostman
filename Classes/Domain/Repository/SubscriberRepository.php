<?php

namespace DMK\Mkpostman\Domain\Repository;

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

use Exception;
use Tx_Rnbase_Domain_Model_Data;
use Tx_Rnbase_Domain_Model_DomainInterface;

\tx_rnbase::load('Tx_Rnbase_Domain_Repository_PersistenceRepository');

/**
 * Subscriber repo.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberRepository extends \Tx_Rnbase_Domain_Repository_PersistenceRepository
{
    /**
     * Liefert den Namen der Suchklasse.
     *
     * @return string
     */
    protected function getSearchClass()
    {
        return 'tx_rnbase_util_SearchGeneric';
    }

    /**
     * Liefert die Model Klasse.
     *
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel';
    }

    /**
     * Adds the subscriber to the sys_category mm
     *
     * @param Tx_Rnbase_Domain_Model_DomainInterface $model
     * @param array|Tx_Rnbase_Domain_Model_Data $options
     *
     * @throws Exception
     *
     * @return void
     */
    public function addToCategories(
        Tx_Rnbase_Domain_Model_DomainInterface $model,
        $categories
    ) {
        if (is_iterable($categories) && count($categories) > 0) {
            $connection = \Tx_Rnbase_Database_Connection::getInstance();
            $connection->doDelete(
                'sys_category_record_mm',
                'uid_foreign = ' . $model->getUid()
            );
            foreach ($categories as $category) {
                $connection->doInsert(
                    'sys_category_record_mm',
                    [
                        'uid_local' => $category,
                        'uid_foreign' => $model->getUid(),
                        'tablenames' => 'tx_mkpostman_subscribers',
                        'fieldname' => 'categories',
                    ]
                );
            }
        }
    }

    /**
     * Finds a subscriber by email.
     *
     * @param int $uid
     *
     * @return Tx_Rnbase_Domain_Model_DomainInterface|null
     */
    public function findByUid(
        $uid
    ) {
        return $this->searchSingle(
            array(
                'SUBSCRIBER.uid' => array(
                    OP_EQ_INT => $uid,
                ),
            ),
            array(
                'enablefieldsbe' => true,
            )
        );
    }

    /**
     * Finds a subscriber by email.
     *
     * @param string $mail
     *
     * @return DMK\Mkpostman\Domain\Model\SubscriberModel|null
     */
    public function findByEmail(
        $mail
    ) {
        return $this->searchSingle(
            array(
                'SUBSCRIBER.email' => array(
                    OP_EQ => $mail,
                ),
            ),
            array(
                'enablefieldsbe' => true,
            )
        );
    }

    /**
     * On default, return hidden and deleted fields in backend.
     *
     * @param array $fields
     * @param array $options
     */
    protected function prepareFieldsAndOptions(
        array &$fields,
        array &$options
    ) {
        parent::prepareFieldsAndOptions($fields, $options);
        $this->prepareGenericSearcher($options);
    }

    /**
     * Prepares the simple generic searcher.
     *
     * @param array $options
     */
    protected function prepareGenericSearcher(
        array &$options
    ) {
        $model = $this->getEmptyModel();

        if (empty($options['searchdef']) || !is_array($options['searchdef'])) {
            $options['searchdef'] = array();
        }

        \tx_rnbase::load('tx_rnbase_util_Arrays');
        $options['searchdef'] = \tx_rnbase_util_Arrays::mergeRecursiveWithOverrule(
            // default searcher config
            array(
                'usealias' => 1,
                'basetable' => $model->getTableName(),
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => get_class($model),
                'alias' => array(
                    'SUBSCRIBER' => array(
                        'table' => $model->getTableName(),
                    ),
                ),
            ),
            // searcher config overrides
            $options['searchdef']
        );
    }
}
