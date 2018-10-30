<?php
namespace DMK\Mkpostman\Form\Handler;

/***************************************************************
 * Copyright notice
 *
 * (c) 2018 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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
 * MK Postman subscribe action
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeHandler extends AbstractFormHandler implements SubscribeFormHandlerInterface
{
    /**
     * @var \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    private $subscriber;

    /**
     * @var array
     */
    private $validationErrors = [];

    /**
     * Renders the subscribtion form
     *
     * @return self
     */
    public function handleForm()
    {
        $this->setToView('handler', 'standalone');

        // create model to fill the form
        $this->subscriber = $this->getSubscriberRepository()->getEmptyModel();
        // force uid to be 0, so it can not be overridden by setProperty!
        $this->subscriber->setUid(0);
        // prefill with current fe user data
        $this->subscriber->setProperty($this->getFeUserData());

        // now check if there are a submit
        if ($this->getParameters()->get('subscribe')) {
            $data = $this->getParameters()->get('subscriber');
            if ($this->validateSubscriberData($data))
            {
                $this->processSubscriberData($data);
            }
            else {
                // prefill with current fe user data
                $this->subscriber->setProperty($data);
            }
        }

        // @TODO: refactor this!
        $this->setToView(
            'form',
            [
                'errorcount' => count($this->validationErrors),
                'errors' => $this->validationErrors,
                'options' => [
                    'gender' => [
                        '' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_general_choose'),
                        '0' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_0'),
                        '1' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_1'),
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Was the form submitted?
     *
     * @return bool
     */
    public function isFinished()
    {
        return (
            empty($this->validationErrors) &&
            $this->getSubscriber() &&
            $this->getSubscriber()->getUid()> 0 &&
            $this->getParameters()->get('subscribe')
        );
    }

    /**
     * @return \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Process the subscriber data after valid form submit
     *
     * @param array $data Form data splitted by tables
     *
     * @return bool
     */
    protected function validateSubscriberData(
        array $data
    ) {
        if (empty($data['email']) || !\Tx_Rnbase_Utility_T3General::validEmail($data['email'])) {
            $this->validationErrors['email'] = true;
            return false;
        }

        return true;
    }

    /**
     * Process the subscriber data after valid form submit
     *
     * @param array $data Form data splitted by tables
     *
     * @return void
     */
    protected function processSubscriberData(
        array $data
    ) {
        // try to find an exciting subscriber
        $subscriber = $this->findOrCreateSubscriber($data);

        // set the data from the form to the model
        foreach ($data as $field => $value) {
            $subscriber->setProperty($field, $value);
        }

        // before a double opt in mail was send, we has to persist the model, we need the uid!
        $this->getSubscriberRepository()->persist($subscriber);

        $this->subscriber = $subscriber;
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
            // a new subscriber initialy is disabled and has to be confirmed
            $subscriber->setDisabled(1);
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
     * The record of the current feuser, if any is logged in.
     *
     * @return array
     */
    protected function getFeUserData()
    {
        return (array) $GLOBALS['TSFE']->fe_user->user;
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
}
