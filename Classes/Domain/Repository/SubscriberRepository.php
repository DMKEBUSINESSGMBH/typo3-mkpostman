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

use DMK\Mkpostman\Domain\Model\CategoryModel;
use DMK\Mkpostman\Domain\Model\SubscriberModel;
use Exception;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\DataModel;
use Sys25\RnBase\Domain\Model\DomainModelInterface;
use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use Sys25\RnBase\Search\SearchBase;
use Sys25\RnBase\Search\SearchGeneric;
use Sys25\RnBase\Utility\Arrays;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Subscriber repo.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberRepository extends PersistenceRepository
{
    /**
     * Liefert den Namen der Suchklasse.
     *
     * @return string
     */
    protected function getSearchClass()
    {
        return SearchGeneric::class;
    }

    /**
     * Liefert den Searcher.
     *
     * Wir nutzen absichtlich nicht den parent, da dieser einen statichen cache aufbaut!
     *
     * @return  SearchBase
     */
    protected function getSearcher()
    {
        return GeneralUtility::makeInstance($this->getSearchClass());
    }

    /**
     * Liefert die Model Klasse.
     *
     * @return string
     */
    protected function getWrapperClass()
    {
        return SubscriberModel::class;
    }

    /**
     * Adds the subscriber to the sys_category mm.
     *
     * @param DomainModelInterface $model
     * @param array|DataModel      $options
     *
     * @throws Exception
     */
    public function addToCategories(
        DomainModelInterface $model,
        $categories
    ) {
        if (is_array($categories) && !empty($categories)) {
            $connection = $this->getDbConnection();
            $connection->doDelete(
                'sys_category_record_mm',
                'uid_foreign = '.$model->getUid()
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
     * @return DomainModelInterface|null
     */
    public function findByUid(
        $uid
    ) {
        return $this->searchSingle(
            [
                'SUBSCRIBER.uid' => [
                    OP_EQ_INT => $uid,
                ],
            ],
            [
                'enablefieldsbe' => true,
            ]
        );
    }

    /**
     * Finds a subscriber by email.
     *
     * @param string $mail
     *
     * @return SubscriberModel|null
     */
    public function findByEmail(
        $mail
    ) {
        return $this->searchSingle(
            [
                'SUBSCRIBER.email' => [
                    OP_EQ => $mail,
                ],
            ],
            [
                'enablefieldsbe' => true,
            ]
        );
    }

    /**
     * Finds subscribers by category.
     *
     * @param CategoryModel $category
     *
     * @return array[DomainInterface]
     */
    public function findByCategory(
        CategoryModel $category
    ) {
        return $this->search(
            [
                'CATEGORYMM.uid_local' => [
                    OP_EQ_INT => $category->getUid(),
                ],
                'CATEGORYMM.tablenames' => [
                    OP_EQ => 'tx_mkpostman_subscribers',
                ],
            ],
            []
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
            $options['searchdef'] = [];
        }

        $options['searchdef'] = Arrays::mergeRecursiveWithOverrule(
            // default searcher config
            [
                'usealias' => 1,
                'basetable' => $model->getTableName(),
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => $this->getWrapperClass(),
                'alias' => [
                    'SUBSCRIBER' => [
                        'table' => $model->getTableName(),
                    ],
                    'CATEGORYMM' => [
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON SUBSCRIBER.uid = CATEGORYMM.uid_foreign',
                    ],
                ],
            ],
            // searcher config overrides
            $options['searchdef']
        );
    }

    /**
     * Provide the Database Connection.
     *
     * @return Connection
     */
    protected function getDbConnection()
    {
        return Connection::getInstance();
    }
}
