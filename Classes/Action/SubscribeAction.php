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

/**
 * MK Postman subscribe action with mkforms
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeAction
    extends AbstractSubscribeAction
{
    /**
     * Renders the subscribtion form
     *
     * @return void
     */
    protected function handleForm()
    {
        $configurations = $this->getConfigurations();
        $confId = $this->getConfId();

        $form = \tx_mkforms_forms_Factory::createForm('mkpostman');

        $form->init(
            $this,
            $configurations->get($confId . 'xml'),
            false,
            $configurations,
            $confId . 'formconfig.'
        );

        $this->setToView('form', $form->render());
        $this->setToView('fullySubmitted', $form->isFullySubmitted());
        $this->setToView('hasValidationErrors', $form->hasValidationErrors());
    }

    /**
     * Prefills the subscribtin form with fe userdada
     *
     * @param    array              $params
     * @param    \tx_ameosformidable $form
     *
     * @return    array
     */
    public function fillForm(array $params, \tx_ameosformidable $form)
    {
        $data = [];

        // prefill with feuserdata,
        // in form we need all values as string to perform some strict checks (gender)!
        $data['subscriber'] = \array_map('strval', $this->getFeUserData());

        return $this->multipleTableStructure2FlatArray(
            $data,
            $form
        );
    }

    /**
     * Only a Wrapper for tx_mkforms_util_FormBase::multipleTableStructure2FlatArray
     *
     * @param array $data
     * @param \tx_ameosformidable $form
     * @return array
     */
    protected function multipleTableStructure2FlatArray(array $data, \tx_ameosformidable $form)
    {
        return \tx_mkforms_util_FormBase::multipleTableStructure2FlatArray(
            $data,
            $form,
            $this->getConfigurations(),
            $this->getConfId()
        );
    }

    /**
     * Process form data
     *
     * @param array              $data
     * @param \tx_ameosformidable $form
     */
    public function processForm($data, &$form)
    {
        // Prepare data
        \tx_rnbase::load('tx_mkforms_util_FormBase');
        $data = \tx_mkforms_util_FormBase::flatArray2MultipleTableStructure(
            $data,
            $form,
            $this->getConfigurations(),
            $this->getConfId()
        );

        $this->processSubscriberData($data['subscriber']);
    }
}
