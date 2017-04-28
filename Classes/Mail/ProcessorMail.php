<?php
namespace DMK\Mkpostman\Mail;

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
 * MK Postman mail processor
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ProcessorMail
{
    /**
     * The config object to use for mails
     *
     * @var \tx_rnbase_configurations
     */
    private $configurations = null;

    /**
     * The Constructor
     *
     * @param \tx_rnbase_configurations $configurations
     */
    public function __construct(
        \tx_rnbase_configurations $configurations
    ) {
        $this->configurations = $configurations;
    }

    /**
     * The configuration object
     *
     * @return \tx_rnbase_configurations
     */
    protected function getConfigurations()
    {
        return $this->configurations;
    }
    /**
     * The conf id
     *
     * @return string
     */
    protected function getConfId()
    {
        return 'mails.';
    }

    /**
     * Is mkmailer loaded? otherwise throw exception
     *
     * @throws BadFunctionCallException
     *
     * @return void
     */
    protected function checkMkmailer()
    {
        \tx_rnbase_util_Extensions::isLoaded('mkmailer', true);
    }

    /**
     * Creates a mailjob based on the receiver and the template object
     *
     * @param \tx_mkmailer_models_Template $template
     *
     * @return \tx_mkmailer_mail_MailJob
     */
    protected function buildJob(
        \tx_mkmailer_models_Template $template = null
    ) {
        /* @var $job \tx_mkmailer_mail_MailJob */
        $job = \tx_rnbase::makeInstance('tx_mkmailer_mail_MailJob');
        if ($template instanceof \tx_mkmailer_models_Template) {
            $job->setFrom($template->getFromAddress());
            $job->setCCs($template->getCcAddress());
            $job->setBCCs($template->getBccAddress());
            $job->setSubject($template->getSubject());
            $job->setContentText($template->getContentText());
            $job->setContentHtml($template->getContentHtml());
        }

        return $job;
    }

    /**
     * Send an activation mail to the user
     *
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
     *
     * @return void
     */
    public function sendSubscriberActivation(
        \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
    ) {
        $this->checkMkmailer();

        \tx_rnbase::load('tx_mkmailer_util_ServiceRegistry');
        $mailSrv = \tx_mkmailer_util_ServiceRegistry::getMailService();

        $mailJob = $this->buildJob(
            $mailSrv->getTemplate('mkpostman_subsciber_activation')
        );

        $receiver = \DMK\Mkpostman\Factory::getSubscriberMailReceiver($subscriber);
        $mailJob->addReceiver($receiver);

        $mailSrv->executeMailJob(
            $mailJob,
            $this->getConfigurations(),
            $this->getConfId()
        );
    }
}
