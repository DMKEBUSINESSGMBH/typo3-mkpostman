<?php
namespace DMK\Mkpostman\Utility;

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

\tx_rnbase::load('tx_rnbase_util_Wizicon');

/**
 * MK Postman Double-Opt-In utility
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DoubleOptInUtility
{
	/**
	 * The subscriber
	 *
	 * @var \DMK\Mkpostman\Domain\Model\SubscriberModel
	 */
	private $subscriber = null;

	/**
	 * The Constructor
	 *
	 * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
	 */
	public function __construct(
		\DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
	) {
		$this->subscriber = $subscriber;
	}

	/**
	 * The current subscriber
	 *
	 * @return  \DMK\Mkpostman\Domain\Model\SubscriberModel
	 */
	protected function getSubscriber()
	{
		return $this->subscriber;
	}

	/**
	 * Updates subscriber model with a new confirmstring
	 *
	 * @return void
	 */
	protected function updateConfirmString()
	{
		$confirmString = $this->createConfirmString();
		$this->getSubscriber()->setConfirmstring($confirmString);
	}

	/**
	 * Creates a new confirmstring for the subscriber
	 *
	 * @return string
	 */
	protected function createConfirmString()
	{
		return md5(\uniqid($this->getSubscriber()->getUid()));
	}

	/**
	 * Creates a activation key for the subscriber
	 * uid:confirmstring:mailmd5
	 *
	 * @param bool $urlencode
	 *
	 * @return string
	 */
	public function buildActivationKey(
		$urlencode = false
	) {
		$subscriber = $this->getSubscriber();

		if (!$subscriber->isPersisted()) {
			throw new \Exception(
				'The subscriber has to ber persisted to create an activation key'
			);
		}
		if (!$subscriber->getEmail()) {
			throw new \Exception(
				'The subscriber neds a email to create an activation key'
			);
		}

		if (!$subscriber->getConfirmstring()) {
			$this->updateConfirmString();
		}

		$key = implode(
			':',
			array(
				$subscriber->getUid(),
				$subscriber->getConfirmstring(),
				md5($subscriber->getEmail())
			)
		);

		// make the key base64 and url encoded
		if ($urlencode) {
			$key = \urlencode(\base64_encode($key));
		}

		return $key;
	}

	/**
	 * Check if the activation key is valid for the current user
	 *
	 * @param string $activationKey
	 *
	 * @return bool
	 */
	public function validateActivationKey(
		$activationKey
	) {
		$subscriber = $this->getSubscriber();

		// the key loks like base64 and urlencoded
		if (\substr_count($activationKey, ':') !== 2) {
			$activationKey = \base64_decode(\urldecode($activationKey));
		}


		list ($uid, $confirmstring, $md5) = explode(':', $activationKey);

		return (
			$subscriber->getUid() == $uid &&
			$subscriber->getConfirmstring() === $confirmstring &&
			md5($subscriber->getEmail()) === $md5
		);
	}
}
