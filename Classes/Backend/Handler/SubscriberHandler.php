<?php
namespace DMK\Mkpostman\Backend\Handler;

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

\tx_rnbase::load('tx_rnbase_mod_IModHandler');

/**
 * Subscriber handler
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class SubscriberHandler
	implements \tx_rnbase_mod_IModHandler
{
	/**
	 * Returns a unique ID for this handler.
	 * This is used to created the subpart in template.
	 *
	 * @return string
	 */
	// @codingStandardsIgnoreStart (interface/abstract mistake)
	public function getSubID()
	{
		// @codingStandardsIgnoreEnd
		return 'mkpostman_subscriber_main';
	}

	/**
	 * Returns the label for Handler in SubMenu. You can use a label-Marker.
	 *
	 * @return string
	 */
	public function getSubLabel()
	{
		return '';
	}

	/**
	 * Returns the handler options
	 *
	 * @param \tx_rnbase_mod_IModule $mod
	 * @param array $options
	 *
	 * @return array
	 */
	protected function prepareOptions(
		\tx_rnbase_mod_IModule $mod,
		array &$options = array()
	) {
		$repo = \DMK\Mkpostman\Factory::getSubscriberRepository();
		$options['baseTableName'] = $repo->getEmptyModel()->getTableName();

		$options['pid'] = $mod->getPid();

		return $options;
	}

	/**
	 * Returns the current object for detail page.
	 *
	 * @return Tx_Rnbase_Domain_Model_RecordInterface
	 */
	protected function getObject()
	{
		return null;
	}

	/**
	 * Display the user interface for this handler
	 *
	 * @param string $template The subpart for handler in func template
	 * @param tx_rnbase_mod_IModule $mod
	 * @param array $options
	 *
	 * @return string
	 */
	// @codingStandardsIgnoreStart (interface/abstract mistake)
	public function showScreen(
		$template,
		\tx_rnbase_mod_IModule $mod,
		$options
	) {
		// @codingStandardsIgnoreEnd
		$this->prepareOptions($mod, $options);

		$markerArray = array();

		$current = $this->getObject();

		$templateMod = \tx_rnbase_util_Templates::getSubpart(
			$template,
			$current ? '###DETAILPART###' : '###SEARCHPART###'
		);

		if ($current) {
			throw new \Exception('detail not implemented yet');
		} else {
			$templateMod = $this->showSearch(
				$templateMod,
				$mod,
				$options,
				$markerArray
			);
		}

		return \tx_rnbase_util_Templates::substituteMarkerArrayCached(
			$templateMod,
			$markerArray
		);
	}

	/**
	 * Base listing
	 *
	 * @param string $template
	 * @param \tx_rnbase_mod_IModule $mod
	 * @param array $options
	 * @param array $markerArray
	 *
	 * @return string
	 */
	protected function showSearch(
		$template,
		\tx_rnbase_mod_IModule $mod,
		array $options,
		array &$markerArray = array()
	) {
		/* @var $searcher \DMK\Mkpostman\Backend\Lister\SubscriberLister */
		$searcher = \tx_rnbase::makeInstance(
			$this->getSearcherClass(),
			$mod,
			$options
		);

		$form = $searcher->getSearchForm();
		$markerArray['###SEARCHFORM###'] = $form;

		$data = $searcher->getResultList();
		$markerArray['###LIST###'] = $data['table'];
		$markerArray['###SIZE###'] = $data['totalsize'];
		$markerArray['###PAGER###'] = $data['pager'];

		$markerArray['###ADDITIONAL###'] = $mod->getFormTool()->createNewLink(
			$options['baseTableName'],
			$options['pid'],
			'###LABEL_BUTTON_NEW_OBJECT###'
		);

		return $template;
	}

	/**
	 * The class for the searcher
	 *
	 * @return string
	 */
	protected function getSearcherClass()
	{
		return 'DMK\\Mkpostman\\Backend\\Lister\\SubscriberLister';
	}

	/**
	 * This method is called each time the method func is clicked,
	 * to handle request data.
	 *
	 * @param \tx_rnbase_mod_IModule $mod
	 *
	 * @return string, with error message
	 */
	public function handleRequest(
		\tx_rnbase_mod_IModule $mod
	) {
		return null;
	}
}
