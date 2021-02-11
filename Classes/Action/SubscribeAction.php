<?php

namespace DMK\Mkpostman\Action;

/***************************************************************
 * Copyright notice
 *
 * (c) 2016-2018 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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

use DMK\Mkpostman\Factory;

/**
 * MK Postman subscribe action.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeAction extends AbstractAction
{
    /**
     * Referrer key after subscribtion success.
     *
     * @var string
     */
    const SUCCESS_REFERRER_SUBSCRIBE = 'subscribe';

    /**
     * Referrer key after subscribtion success.
     *
     * @var string
     */
    const SUCCESS_REFERRER_UNSUBSCRIBE = 'unsubscribe';

    /**
     * Referrer key after activation success.
     *
     * @var string
     */
    const SUCCESS_REFERRER_ACTIVATE = 'activate';

    /**
     * Start the dance...
     *
     * @param \tx_rnbase_parameters     $parameters
     * @param \tx_rnbase_configurations $configurations
     * @param \ArrayObject              $viewData
     *
     * @return string|null
     */
    public function doRequest()
    {
        $parameters = $this->getParameters();

        // check for an subscriber activation
        $key = $parameters->get('key');
        if (!empty($key) && $this->handleActivation($key)) {
            return null;
        }

        // check for an unsubscribe
        $key = $parameters->get('unsubscribe');
        if (!empty($key) && $this->handleUsubscribe($key)) {
            return null;
        }

        // check for success after a subscription or activation
        $success = $parameters->get('success');
        if (!empty($success) && $this->handleSuccess($success)) {
            return null;
        }

        // render the subscribtion form
        return $this->handleForm();
    }

    /**
     * Activates a subscriber by key.
     *
     * @param string $activationKey
     *
     * @return bool
     */
    protected function handleActivation(
        $activationKey
    ) {
        try {
            $doubleOptInUtil = \DMK\Mkpostman\Factory::getDoubleOptInUtility(
                $activationKey
            );
        } catch (\BadMethodCallException $e) {
            if (1464951846 != $e->getCode()) {
                throw $e;
            }

            return false;
        }

        if ($doubleOptInUtil->activateByKey($activationKey)) {
            // after a successful activation we perform a redirect to success page
            $this->performSuccessRedirect(
                self::SUCCESS_REFERRER_ACTIVATE,
                $doubleOptInUtil->getSubscriber()
            );
        }
    }

    /**
     * Disables a subscriber by key.
     *
     * @param string $activationKey
     *
     * @return bool
     */
    protected function handleUsubscribe(
        $activationKey
    ) {
        try {
            $doubleOptInUtil = \DMK\Mkpostman\Factory::getDoubleOptInUtility(
                $activationKey
            );
        } catch (\BadMethodCallException $e) {
            if (1464951846 != $e->getCode()) {
                throw $e;
            }

            return false;
        }

        if ($doubleOptInUtil->deactivateByKey($activationKey)) {
            // after a successful activation we perform a redirect to success page
            $this->performSuccessRedirect(
                self::SUCCESS_REFERRER_UNSUBSCRIBE,
                $doubleOptInUtil->getSubscriber()
            );
        }
    }

    /**
     * Activates a subscriber by key.
     *
     * @param string $success
     *
     * @return bool
     */
    protected function handleSuccess(
        $success
    ) {
        $success = \DMK\Mkpostman\Factory::getCryptUtility()->urlDencode($success);
        list($referrer, $uid) = explode(':', $success);

        switch ($referrer) {
            case self::SUCCESS_REFERRER_SUBSCRIBE:
                break;

            case self::SUCCESS_REFERRER_ACTIVATE:
                break;

            case self::SUCCESS_REFERRER_UNSUBSCRIBE:
                break;

            default:
                return false;
        }

        $this->setToView(
            'main_view_key',
            'success_'.$referrer
        );

        $this->setToView(
            'subscriber',
            $this->getSubscriberRepository()->findByUid($uid)
        );

        return true;
    }

    /**
     * Renders the subscribtion form.
     */
    protected function handleForm()
    {
        $handlerClass = $this->getConfigurations()->get($this->getTemplateName().'FormHandler');

        /* @var $handler \DMK\Mkpostman\Form\Handler\SubscribeFormHandlerInterface */
        $handler = \tx_rnbase::makeInstance($handlerClass, $this);

        if (!$handler instanceof \DMK\Mkpostman\Form\Handler\SubscribeFormHandlerInterface) {
            throw new \LogicException('Invalid subscribe form handler found.');
        }

        $handler->handleForm();

        $categories = $this->getCategories();
        $this->setToView('categories', $categories);

        $subscriber = $handler->getSubscriber();
        $this->setToView('subscriber', $subscriber);

        if (!$handler->isFinished()) {
            return;
        }

        // if there is a new subscriber or the exciting is disabled, send double opt in
        if ($subscriber->isHidden()) {
            $this->performDoubleOptIn($subscriber);
        }

        $this->getSubscriberRepository()->persist($subscriber);

        // after a successful submit we perform a redirect to success page
        // if the subscriber is already subscribed, send activated, otherwise subscribed
        $this->performSuccessRedirect(
            ($subscriber->isHidden() ?
                self::SUCCESS_REFERRER_SUBSCRIBE :
                self::SUCCESS_REFERRER_ACTIVATE
            ),
            $subscriber
        );
    }

    /**
     * Gets the categories.
     *
     * @return array
     */
    protected function getCategories()
    {
        $categoryIds = $this->getConfigurations()->getExploded($this->getConfId().'categories');
        if (!empty($categoryIds)) {
            $catRepo = Factory::getCategoryRepository();
            $categories = [];
            foreach ($categoryIds as $categoryId) {
                $categories[] = $catRepo->findByUid($categoryId);
            }
        }

        return $categories;
    }

    /**
     * Sends the double opt in mail.
     *
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
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
     * Performs a sucess redirect.
     *
     * @param string                                      $referrer
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
     */
    protected function performSuccessRedirect(
        $referrer,
        \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
    ) {
        $link = $this->getConfigurations()->createLink();
        $link->initByTS(
            $this->getConfigurations(),
            $this->getConfId().'redirect.'.$referrer.'.',
            [
                'success' => \DMK\Mkpostman\Factory::getCryptUtility()->urlEncode(
                    $referrer.':'.$subscriber->getUid()
                ),
            ]
        );

        $link->redirect();
    }

    /**
     * Should we use the lagacy marker template?
     *
     * @return bool
     */
    protected function isLegacyTemplate()
    {
        return $this->getConfigurations()->getBool($this->getConfId().'legacyTemplate');
    }

    /**
     * Viewclassname.
     *
     * @return string
     */
    protected function getViewClassName()
    {
        if ($this->isLegacyTemplate()) {
            return 'DMK\\Mkpostman\\View\\SubscribeView';
        }

        return 'Sys25\\RnBase\\Fluid\\View\\Action';
    }

    /**
     * Liefert den Pfad zum Template.
     *
     * @return string
     */
    protected function getTemplateFile()
    {
        // use the old template from `subscribeTemplate`
        if ($this->isLegacyTemplate()) {
            return $this->getConfigurations()->get(
                $this->getTemplateName().'Template', true
            );
        }

        // use the new template from `subscribe.template.file`
        return $this->getConfigurations()->get(
            $this->getConfId().'template.file', true
        );
    }

    /**
     * Returns the subscriber repository.
     *
     * @return \DMK\Mkpostman\Domain\Repository\SubscriberRepository
     */
    protected function getSubscriberRepository()
    {
        return \DMK\Mkpostman\Factory::getSubscriberRepository();
    }

    /**
     * Confid.
     *
     * @return string
     */
    public function getConfId()
    {
        return 'subscribe.';
    }

    /**
     * Templatename and confid.
     *
     * @return string
     */
    protected function getTemplateName()
    {
        return 'subscribe';
    }
}
