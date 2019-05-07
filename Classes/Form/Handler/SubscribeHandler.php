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
class SubscribeHandler extends AbstractSubscribeHandler
{
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
        $this->setSubscriber($this->getSubscriberRepository()->getEmptyModel());
        // force uid to be 0, so it can not be overridden by setProperty!
        $this->getSubscriber()->setUid(0);
        $this->getSubscriber()->setProperty('categories', []);
        // prefill with current fe user data
        $this->getSubscriber()->setProperty($this->getFeUserData());

        // now check if there are a submit
        if ($this->getParameters()->get('subscribe')) {
            $data = $this->getParameters()->get('subscriber');
            if ($this->validateSubscriberData($data))
            {
                $this->processSubscriberData($data);
            }
            else {
                // prefill with current fe user data
                $this->getSubscriber()->setProperty($data);
            }
        }

        $this->setToView(
            'form',
            [
                'errorcount' => count($this->validationErrors),
                'errors' => $this->validationErrors,
                'options' => $this->getFormSelectOptions(),
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
     * Returns the options for the select fields in the template
     *
     * @return array
     */
    protected function getFormSelectOptions() {
        return [
            'gender' => [
                '' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_general_choose'),
                '0' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_0'),
                '1' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_1'),
            ]
        ];
    }
}
