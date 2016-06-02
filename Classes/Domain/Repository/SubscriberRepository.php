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

\tx_rnbase::load('Tx_Rnbase_Domain_Repository_PersistenceRepository');

/**
 * Subscriber repo
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberRepository
	extends \Tx_Rnbase_Domain_Repository_PersistenceRepository
{
	/**
	 * Liefert den Namen der Suchklasse
	 *
	 * @return 	string
	 */
	protected function getSearchClass()
	{
		return 'tx_rnbase_util_SearchGeneric';
	}

	/**
	 * Liefert die Model Klasse.
	 *
	 * @return 	string
	 */
	protected function getWrapperClass()
	{
		return 'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel';
	}

	/**
	 * Finds a subscriber by email
	 *
	 * @param string $mail
	 *
	 * @return null|DMK\Mkpostman\Domain\Model\SubscriberModel
	 */
	public function findByEmail(
		$mail
	) {
		return $this->searchSingle(
			array (
				'SUBSCRIBER.email' => array(
					OP_EQ => $mail
				)
			),
			array(
				'enablefieldsbe' => true
			)
		);
	}

	/**
	 * On default, return hidden and deleted fields in backend
	 *
	 * @param array $fields
	 * @param array $options
	 *
	 * @return void
	 */
	protected function prepareFieldsAndOptions(
		array &$fields,
		array &$options
	) {
		$this->handleEnableFieldsOptions($fields, $options);
		$this->prepareGenericSearcher($options);
	}

	/**
	 * Prepares the simple generic searcher
	 *
	 * @param array $options
	 *
	 * @return void
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
						'table' => $model->getTableName()
					)
				)
			),
			// searcher config overrides
			$options['searchdef']
		);
	}
}
