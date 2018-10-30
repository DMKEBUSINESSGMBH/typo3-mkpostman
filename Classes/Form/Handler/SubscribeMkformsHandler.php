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
class SubscribeMkformsHandler extends AbstractSubscribeHandler
{
    /**
     * @var \tx_mkforms_forms_Base
     */
    private $form;

    /**
     * Renders the subscribtion form
     *
     * @return self
     */
    public function handleForm()
    {
        $form = $this->createForm();

        $this->setToView('form', $form->render());
        $this->setToView('handler', 'mkforms');

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
    public function isFinished()
    {
        return $this->getSubscriber() !== null && $this->getForm()->isFullySubmitted();
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
