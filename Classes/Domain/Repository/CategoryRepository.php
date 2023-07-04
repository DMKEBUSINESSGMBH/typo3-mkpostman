<?php

namespace DMK\Mkpostman\Domain\Repository;

use Sys25\RnBase\Domain\Collection\BaseCollection;
use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use Sys25\RnBase\Search\SearchGeneric;
use Sys25\RnBase\Utility\Arrays;

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
 * Category repo.
 *
 * @author Markus Crasser
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CategoryRepository extends PersistenceRepository
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
     * Liefert die Model Klasse.
     *
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'DMK\\Mkpostman\\Domain\\Model\\CategoryModel';
    }

    /**
     * Finds a Category by uid.
     *
     * @param int $uid
     *
     * @return \DMK\Mkpostman\Domain\Model\CategoryModel|null
     */
    public function findByUid(
        $uid
    ) {
        return $this->searchSingle(
            [
                'CATEGORY.uid' => [
                    OP_EQ_INT => $uid,
                ],
            ],
            [
                'enablefieldsbe' => true,
            ]
        );
    }

    /**
     * Finds Categories by subscriber.
     *
     * @param int $subscriberId
     *
     * @return BaseCollection
     */
    public function findBySubscriberId(
        $subscriberId
    ) {
        return $this->search(
            [
                'CATEGORYMM.uid_foreign' => [
                    OP_EQ_INT => $subscriberId,
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
                'basetablealias' => 'CATEGORY',
                'wrapperclass' => get_class($model),
                'alias' => [
                    'CATEGORY' => [
                        'table' => $model->getTableName(),
                    ],
                    'CATEGORYMM' => [
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON CATEGORY.uid = CATEGORYMM.uid_local',
                    ],
                ],
            ],
            // searcher config overrides
            $options['searchdef']
        );
    }
}
