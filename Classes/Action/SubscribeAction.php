<?php
namespace DMK\Mkpostman\Action;

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

\tx_rnbase::load('tx_mkforms_action_FormBase');

/**
 * MK Postman subscribe action
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeAction
	extends \tx_mkforms_action_FormBase
{
	/**
	 * The record of the current feuser, if any is logged in.
	 *
	 * @return array
	 */
	protected function getFeUserData()
	{
		return (array) $GLOBALS['TSFE']->fe_user->user;
	}

	/**
	 * Actually fill the data to be published in form
	 *
	 * @param array $params Parameters from the form
	 *
	 * @return array
	 */
	protected function fillData(
		array $params
	) {
		// prefill with feuserdata, in Form we need all values as string to perform some strict checks (gender)!
		$params['subscriber'] = \array_map('strval', $this->getFeUserData());

		return $params;
	}

	/**
	 * Process the subscriber data after valid form submit
	 *
	 * @param array $data Form data splitted by tables
	 *
	 * @return array
	 */
	protected function processSubscriberData(
		array $data
	) {
		$repo = \DMK\Mkpostman\Factory::getSubscriberRepository();

		// try to find an exciting subscriber
		$subscriber = $this->findOrCreateSubscriber($data);

		// set the data from the form to the model
		foreach ($data as $field => $value) {
			$subscriber->setProperty($field, $value);
		}

		// before a double opt in mail was send, we has to persist the model, we need the uid!
		$repo->persist($subscriber);

		// if there is a new subscriber or the exciting is hidden, send double opt in
		if ($subscriber->isHidden()) {
			$this->performDoubleOptIn($subscriber);
		}

		$repo->persist($subscriber);

		return $data;
	}

	/**
	 * Finds an exsisting subscriber by mail or creates a new one.
	 *
	 * @param array $data
	 *
	 * @return \DMK\Mkpostman\Domain\Model\SubscriberModel
	 */
	protected function findOrCreateSubscriber(
		array $data = array()
	) {
		$repo = $this->getSubscriberRepository();

		// try to find an exciting subscriber
		if (!empty($data['email'])) {
			$subscriber = $repo->findByEmail($data['email']);
		}

		// otherwise create a new one
		if (!$subscriber) {
			$subscriber = $repo->createNewModel();
			// a new subscriber initialy is hidden and has to be confirmed
			$subscriber->setHidden(1);
			// set the storage pid for the new subscriber
			$subscriber->setPid(
				$this->getConfigurations()->getInt(
					$this->getConfId() . 'subscriber.storage'
				)
			);
		}

		return $subscriber;
	}

	/**
	 * Sends the double opt in mail
	 *
	 * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
	 *
	 * @return void
	 */
	protected function performDoubleOptIn(
		\DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
	) {
		$processor = \DMK\Mkpostman\Factory::getProcessorMail(
			$this->getConfigurations()
		);
		$processor->sendSubscriberActivation($subscriber);
	}

	/**
	 * Returns the subscriber repository
	 *
	 * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
	 */
	protected function getSubscriberRepository()
	{
		return \DMK\Mkpostman\Factory::getSubscriberRepository();
	}

	/**
	 * Confid
	 *
	 * @return string
	 */
	public function getConfId()
	{
		return 'subscribe.';
	}

	/**
	 * Templatename and confid
	 *
	 * @return string
	 */
	protected function getTemplateName()
	{
		return 'subscribe';
	}

	/**
	 * Viewclassname
	 *
	 * @return string
	 */
	protected function getViewClassName()
	{
		return 'DMK\\Mkpostman\\View\\SubscribeView';
	}
}
