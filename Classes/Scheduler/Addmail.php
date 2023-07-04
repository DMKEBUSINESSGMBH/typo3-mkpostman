<?php

namespace DMK\Mkpostman\Scheduler;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class Addmail extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
    public function execute() {

        $mailSrv = \tx_mkmailer_util_ServiceRegistry::getMailService();
        $templateKey = 'mkpostman_subsciber_activation'; // Der Key ist abhängig von der Applikation. Das entsprechende Template muss im BE angelegt sein
        $templateObj = $mailSrv->getTemplate($templateKey);

//        DebuggerUtility::var_dump($templateObj->getContentText());

        $from = 'test@egal.de';
        $from = new \tx_mkmailer_mail_Address('test@egal.de');

        // Den Empfänger der Mail als Receiver anlegen, Hier ein Standardreceiver, man kann aber auch eigene Receiver schreiben
        $receiver = new \tx_mkmailer_receiver_Email();
        $receiver->setEMail('test@test.de');

        $job = \tx_rnbase::makeInstance('tx_mkmailer_mail_MailJob');
        $job->addReceiver($receiver);
        $job->setFrom($from);
//        $job->setCCs($templateObj->getCcAddress());
//        $job->setBCCs($templateObj->getBccAddress());

        $txtPart = $templateObj->getContentText();
        $htmlPart = $templateObj->getContentHtml();
        $subject = $templateObj->getSubject();
//        $txtPart = 'test';
//        $htmlPart = 'test';
//        $subject = 'test';
// Die Mailinhalte können jetzt durch verschiedene zusätzliche Marker geschickt werden, um Platzhalter zu ersetzen.
// Der FEUser wird beim Versand schon automatisch ersetzt! Es geht also nur um zusätzliche Daten.

        $job->setSubject($subject);
        $job->setContentText($txtPart);
        $job->setContentHtml($htmlPart);


// Und nun geht alles in den Versand
        $mailSrv->spoolMailJob($job);
return true;
    }
}

