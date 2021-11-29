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
 * MK Postman subscribe action.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeHandler extends AbstractSubscribeHandler
{
    /**
     * @var array
     */
    protected $validationErrors = [];

    /**
     * Renders the subscribtion form.
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

        $honeyPot = '';
        if ($this->isHoneyputEnabled()) {
            $name = $this->getHoneypotFieldName();
            $honeyPot = '
<input type="text" autocomplete="off" tabindex="-1" id="mkpostman[subscriber]['.$name.']" name="mkpostman[subscriber]['.$name.']"/>
';
        }

        // now check if there are a submit
        if ($this->getParameters()->get('subscribe')) {
            $data = $this->getParameters()->get('subscriber');
            if ($this->validateSubscriberData($data)) {
                $this->processSubscriberData($data);
            } else {
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
                'honeyPot' => $honeyPot,
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
        return
            empty($this->validationErrors) &&
            $this->getSubscriber() &&
            $this->getSubscriber()->getUid() > 0 &&
            $this->getParameters()->get('subscribe')
        ;
    }

    /**
     * Process the subscriber data after valid form submit.
     *
     * @param array $data Form data splitted by tables
     *
     * @return bool
     */
    protected function validateSubscriberData(
        array $data
    ) {
        if (empty($data['email']) || !\Tx_Rnbase_Utility_T3General::validEmail($data['email'])) {
            $this->setFieldInvalid('email');
        }

        if (($minCats = $this->getConfigurations()->getInt($this->getConfId().'requiredcategoriesmin')) > 0) {
            if (empty($data['categories']) || !is_array($data['categories']) || count($data['categories']) < $minCats) {
                $this->setFieldInvalid('categories', null, ['%requiredmin%' => $minCats]);
            }
        }

        if ($this->isHoneyputEnabled()) {
            $name = $this->getHoneypotFieldName();
            if (!empty($data[$name])) {
                $this->setFieldInvalid('honeypot');
            }
        }

        return empty($this->validationErrors);
    }

    /**
     * Marks a field as invalid and adds translated error message.
     *
     * @param $field
     * @param array $args
     */
    protected function setFieldInvalid($field, $label = null, array $args = [])
    {
        $label = $label ?: 'label_form_subscriber_'.$field.'_required';
        $this->validationErrors[$field] = strtr(
            $this->getConfigurations()->getCfgOrLL($label),
            $args
        );
    }

    /**
     * Marks a field as invalid and adds translated error message.
     *
     * @return string
     */
    protected function getHoneypotFieldName()
    {
        return $this->getConfigurations()->get($this->getConfId().'honeypotFieldname');
    }

    /**
     * Marks a field as invalid and adds translated error message.
     *
     * @return bool
     */
    protected function isHoneyputEnabled()
    {
        return (bool) $this->getConfigurations()->getInt($this->getConfId().'honeypot');
    }

    /**
     * Returns the options for the select fields in the template.
     *
     * @return array
     */
    protected function getFormSelectOptions()
    {
        return [
            'gender' => [
                '' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_general_choose'),
                '0' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_0'),
                '1' => \tx_rnbase_util_Lang::sL('LLL:EXT:mkpostman/Resources/Private/Language/Frontend.xlf:label_gender_1'),
            ],
        ];
    }
}
