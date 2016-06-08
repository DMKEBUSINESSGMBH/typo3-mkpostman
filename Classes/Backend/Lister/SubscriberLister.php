<?php
namespace DMK\Mkpostman\Backend\Lister;

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

use \DMK\Mkpostman\Backend\Decorator\SubscriberDecorator;

\tx_rnbase::load('tx_rnbase_mod_base_Lister');

/**
 * Subscriber lister
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class SubscriberLister
	extends \tx_rnbase_mod_base_Lister
{
	/**
	 * The Subscriber repository
	 *
	 * @return Tx_Rnbase_Domain_Repository_InterfaceSearch
	 */
	protected function getRepository()
	{
		return \DMK\Mkpostman\Factory::getSubscriberRepository();
	}

	/**
	 * Only a wraper for getRepository to expect the abstract base class.
	 *
	 * @return Tx_Rnbase_Domain_Repository_InterfaceSearch
	 */
	protected function getService()
	{
		return $this->getRepository();
	}

	/**
	 * Liefert die Spalten, in denen gesucht werden soll
	 *
	 * @return array
	 */
	protected function getSearchColumns()
	{
		return array(
			'SUBSCRIBER.uid',
			'SUBSCRIBER.first_name',
			'SUBSCRIBER.last_name',
			'SUBSCRIBER.email',
		);
	}

	/**
	 * Returns the complete search form
	 *
	 * @return 	string
	 */
	public function getSearchForm()
	{
		$data = array();
		$options = array('pid' => $this->options['pid']);

		$this->setFilterValue(
			'searchword',
			$this->showFreeTextSearchForm(
				$data['search'],
				$this->getSearcherId() . 'Search',
				$options
			)
		);


		$this->setFilterValue(
			'disabled',
			$this->showDisabledSelector(
				$data['disabled'],
				$options
			)
		);

		$data['updatebutton'] = array(
			'label' => '',
			'button' => $this->getSearchButton()
		);

		return $this->buildFilterTable($data);
	}

	/**
	 *
	 * @param unknown $marker
	 * @param array $options
	 */
	protected function showDisabledSelector(&$marker, $options=array()) {
		$items = array(
				0 => '###LABEL_FILTER_STATE_0###',
				1 => '###LABEL_FILTER_STATE_1###',
		);
		\tx_rnbase::load('tx_rnbase_mod_Util');
		$selectedItem = \tx_rnbase_mod_Util::getModuleValue(
			'showdisabled',
			$this->getModule(),
			array('changed' => \tx_rnbase_parameters::getPostOrGetParameter('SET'))
		);

		$options['label'] = '###LABEL_FILTER_STATE###';
		return \tx_rnbase_mod_Util::showSelectorByArray(
			$items,
			$selectedItem,
			'showdisabled',
			$marker,
			$options
		);
	}

	/**
	 * Initializes the fields and options for the repository search
	 *
	 * @param array $fields
	 * @param array $options
	 */
	protected function prepareFieldsAndOptions(
		array &$fields,
		array &$options
	) {
		parent::prepareFieldsAndOptions($fields, $options);

		if (isset($this->options['pid'])) {
			$fields['SUBSCRIBER.pid'][OP_EQ_INT] = $this->options['pid'];
		}

		if ($this->getFilterValue('disabled')) {
			$options['enablefieldsbe'] = 1;
		} else {
			$options['enablefieldsfe'] = 1;
		}
	}

	/**
	 * The decorator to render the rows
	 *
	 * @return SubscriberDecorator
	 */
	protected function createDefaultDecorator()
	{
		return \tx_rnbase::makeInstance(
			'DMK\\Mkpostman\\Backend\\Decorator\\SubscriberDecorator',
			$this->getModule()
		);
	}

	/**
	 * Liefert die Spalten für den Decorator.
	 *
	 * @param SubscriberDecorator $decorator
	 *
	 * @return array
	 */
	protected function getColumns(SubscriberDecorator $decorator)
	{
		$columns = array();

		($this
			->addDecoratorColumnEmail($columns, $decorator)
			->addDecoratorColumnName($columns, $decorator)
			->addDecoratorColumnActions($columns, $decorator)
		);

		return $columns;
	}

	/**
	 * Adds the column 'uid' to the be list.
	 *
	 * @param array $columns
	 * @param SubscriberDecorator $decorator
	 *
	 * @return SubscriberLister
	 */
	protected function addDecoratorColumnEmail(
		array &$columns,
		SubscriberDecorator $decorator = null
	) {
		$columns['email'] = array(
			'title' => 'label_tableheader_email',
			'decorator' => $decorator,
		);

		return $this;
	}

	/**
	 * Adds the column 'uid' to the be list.
	 *
	 * @param array $columns
	 * @param SubscriberDecorator $decorator
	 *
	 * @return SubscriberLister
	 */
	protected function addDecoratorColumnName(
		array &$columns,
		SubscriberDecorator $decorator = null
	) {
		$columns['name'] = array(
			'title' => 'label_tableheader_name',
			'decorator' => $decorator,
		);

		return $this;
	}

	/**
	 * Adds the column 'actions' to the be list.
	 * this column contains the edit, hide, remove, ... actions.
	 *
	 * @param array $columns
	 * @param SubscriberDecorator $decorator
	 *
	 * @return SubscriberLister
	 */
	protected function addDecoratorColumnActions(
		array &$columns,
		SubscriberDecorator $decorator = null
	) {
		$columns['actions'] = array(
			'title' => 'label_tableheader_actions',
			'decorator' => $decorator,
		);

		return $this;
	}
}
