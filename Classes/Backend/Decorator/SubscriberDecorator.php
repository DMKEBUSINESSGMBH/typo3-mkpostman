<?php
namespace DMK\Mkpostman\Backend\Decorator;

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
 * Subscriber lister
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class SubscriberDecorator
{
	/**
	 * The module
	 *
	 * @var \tx_rnbase_mod_BaseModule
	 */
	private $mod = null;

	/**
	 * Constructor
	 *
	 * @param \tx_rnbase_mod_BaseModule $mod
	 */
	public function __construct(
		\tx_rnbase_mod_BaseModule $mod
	) {
		$this->mod = $mod;
	}

	/**
	 * Returns the module
	 *
	 * @return tx_rnbase_mod_IModule
	 */
	protected function getModule()
	{
		return $this->mod;
	}

	/**
	 * Returns an instance of tx_rnbase_mod_IModule
	 *
	 * @return \tx_rnbase_util_FormTool
	 */
	protected function getFormTool()
	{
		return $this->mod->getFormTool();
	}

	/**
	 * Formats a value
	 *
	 * @param string $columnValue
	 * @param string $columnName
	 * @param array $record
	 * @param \Tx_Rnbase_Domain_Model_DataInterface $entry
	 *
	 * @return string
	 */
	public function format(
		$columnValue,
		$columnName,
		array $record,
		\Tx_Rnbase_Domain_Model_DataInterface $entry
	) {
		$return = $columnValue;

		switch ($columnName) {
			case 'email':
				$return = $this->getEmailColumn($entry);
				break;

			case 'name':
				$return = $this->getNameColumn($entry);
				break;

			case 'actions':
				$return = $this->getActions($entry);
				break;

			default:
				break;
		}

		return sprintf(
			'<span style="color:%3$s">%2$s</span>',
			CRLF,
			$return,
			$entry->getDisabled() ? '#600' : '#060'
		);
	}

	/**
	 * Renders the useractions
	 *
	 * @param \Tx_Rnbase_Domain_Model_DataInterface $item
	 *
	 * @return string
	 */
	protected function getActions(
		\Tx_Rnbase_Domain_Model_DataInterface $item
	) {
		\tx_rnbase::load('tx_rnbase_util_TCA');

		$return = '';

		$tableName = $item->getTableName();
		// we use the real uid, not the uid of the parent!
		$uid = $item->getProperty('uid');

		$return .= $this->getFormTool()->createEditLink(
			$tableName,
			$uid,
			''
		);

		$return .= $this->getFormTool()->createHideLink(
			$tableName,
			$uid,
			$item->getDisabled()
		);

		$return .= $this->getFormTool()->createDeleteLink(
			$tableName,
			$uid,
			'',
			array(
				'confirm' => '###LABEL_SUBSCRIBER_DELETE_CONFIRM###'

			)
		);

		return $return;
	}

	/**
	 * Renders the label column.
	 *
	 * @param \Tx_Rnbase_Domain_Model_DataInterface $item
	 *
	 * @return string
	 */
	protected function getEmailColumn(
		\Tx_Rnbase_Domain_Model_DataInterface $item
	) {

		$lastModifyDateTime = $item->getLastModifyDateTime();
		$creationDateTime = $item->getCreationDateTime();

		return sprintf(
			'<span title="UID: %3$d %1$sCreation: %4$s %1$sLast Change: %5$s">%2$s</span>',
			CRLF,
			$item->getEmail(),
			$item->getProperty('uid'),
			$creationDateTime ? $creationDateTime->format(\DateTime::ATOM) : '-',
			$lastModifyDateTime ? $lastModifyDateTime->format(\DateTime::ATOM) : '-'
		);
	}
	/**
	 * Renders the label column.
	 *
	 * @param \Tx_Rnbase_Domain_Model_DataInterface $item
	 *
	 * @return string
	 */
	protected function getNameColumn(
		\Tx_Rnbase_Domain_Model_DataInterface $item
	) {
		$title = array_filter(
			array(
				\tx_rnbase_util_Lang::sL(
					'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:' .
						'tx_mkpostman_subscribers.gender.' . (int) $item->getGender()
				),
				$item->getFirstName(),
				$item->getLastName()
			)
		);



		if (count($title) > 1) {
			$title = implode(' ', $title);
		} else {
			$title = 'unknown';
		}

		return $title;
	}
}
