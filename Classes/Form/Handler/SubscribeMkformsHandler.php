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
 * MK Postman subscribe action with mkforms
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeMkformsHandler extends AbstractFormHandler implements SubscribeFormHandlerInterface
{
    /**
     * @var \tx_mkforms_forms_Base
     */
    private $form;

    /**
     * @var \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    private $subscriber;

    /**
     * Renders the subscribtion form
     *
     * @return self
     */
    public function handleForm()
    {
        $form = $this->createForm();

        $this->setToView('form', $form->render());

        return $this;
    }

    /**
     * Creates the Form
     *
     * @return \tx_mkforms_forms_Base
     */
    protected function createForm()
    {
        $configurations = $this->getConfigurations();
        $confId = $this->getConfId();

        $this->form = \tx_mkforms_forms_Factory::createForm('mkpostman');

        $this->form->init(
            $this,
            $configurations->get($confId . 'xml'),
            false,
            $configurations,
            $confId . 'formconfig.'
        );

        return $this->form;
    }

    /**
     * @return \tx_mkforms_forms_Base
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * Was the form submitted?
     *
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->getForm()->isFullySubmitted();
    }

    /**
     * @return \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Prefills the subscribtin form with fe userdada.
     *
     * Is called by the mkforms subscribe form xml
     *
     * @param array $params
     *
     * @return array
     */
    public function fillForm(array $params)
    {
        $data = [];

        // prefill with feuserdata,
        // in form we need all values as string to perform some strict checks (gender)!
        $data['subscriber'] = \array_map('strval', $this->getFeUserData());

        return $this->multipleTableStructure2FlatArray($data);
    }

    /**
     * Only a Wrapper for tx_mkforms_util_FormBase::multipleTableStructure2FlatArray
     *
     * @param array $data
     *
     * @return array
     */
    protected function multipleTableStructure2FlatArray(array $data)
    {
        return \tx_mkforms_util_FormBase::multipleTableStructure2FlatArray(
            $data,
            $this->getForm(),
            $this->getConfigurations(),
            $this->getConfId()
        );
    }

    /**
     * Process form data.
     *
     * Is called by the mkforms subscribe form xml
     *
     * @param array $data
     */
    public function processForm($data)
    {
        // Prepare data
        \tx_rnbase::load('tx_mkforms_util_FormBase');
        $data = \tx_mkforms_util_FormBase::flatArray2MultipleTableStructure(
            $data,
            $this->getForm(),
            $this->getConfigurations(),
            $this->getConfId()
        );

        $this->processSubscriberData($data['subscriber']);
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
     * Returns the subscriber repository
     *
     * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
     */
    protected function getSubscriberRepository()
    {
        return \DMK\Mkpostman\Factory::getSubscriberRepository();
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
     * The TemplatePath for the mkforms xml subscribe form
     *
     * @return string
     */
    public function getFormTemplate()
    {
        \tx_rnbase::load('tx_rnbase_util_Files');

        return \tx_rnbase_util_Files::getFileAbsFileName(
            $this->getConfigurations()->get(
                'subscribeTemplate',
                true
            )
        );
    }
}
