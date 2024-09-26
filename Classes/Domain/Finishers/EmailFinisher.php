<?php
namespace Madj2k\Forminator\Domain\Finishers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Service\TranslationService;

/**
 * Class EmailFinisher
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EmailFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = [
        'recipientName' => '',
        'senderName' => '',
        'addHtmlPart' => true,
        'attachUploads' => true,
        'assignOptions' => []
    ];

    /**
     * Executes this finisher
	 *
     * @throws \TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException
	 * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
	 * @see AbstractFinisher::execute()
	 */
    protected function executeInternal(): void
	{
        $languageBackup = null;
        // Flexform overrides write strings instead of integers so
        // we need to cast the string '0' to false.
        if (
            isset($this->options['addHtmlPart'])
            && $this->options['addHtmlPart'] === '0'
        ) {
            $this->options['addHtmlPart'] = false;
        }

        $version = GeneralUtility::makeInstance(Typo3Version::class);
        /** todo: remove when support for TYPO3 v10 is dropped */
        if ($version->getMajorVersion() < 11) {
            $recipients = $this->getRecipients('recipients', 'recipientAddress', 'recipientName');
            $replyToRecipients = $this->getRecipients('replyToRecipients', 'replyToAddress');
            $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients', 'carbonCopyAddress');
            $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients', 'blindCarbonCopyAddress');
        } else {
            $recipients = $this->getRecipients('recipients');
            $replyToRecipients = $this->getRecipients('replyToRecipients');
            $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients');
            $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients');
        }

        $subject = (string)$this->parseOption('subject');
        $senderAddress = $this->parseOption('senderAddress');
        $senderAddress = is_string($senderAddress) ? $senderAddress : '';
        $senderName = $this->parseOption('senderName');
        $senderName = is_string($senderName) ? $senderName : '';
        $addHtmlPart = $this->parseOption('addHtmlPart') ? true : false;
        $attachUploads = $this->parseOption('attachUploads');
        $title = (string)$this->parseOption('title') ?: $subject;

        if ($subject === '') {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipients)) {
            throw new FinisherException('The option "recipients" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $formRuntime = $this->finisherContext->getFormRuntime();

        $translationService = GeneralUtility::makeInstance(TranslationService::class);
        if (is_string($this->options['translation']['language'] ?? null) && $this->options['translation']['language'] !== '') {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }

        $mail = $this
            ->initializeFluidEmail($formRuntime)
            ->from(new Address($senderAddress, $senderName))
            ->to(...$recipients)
            ->subject($subject)
            ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
            ->assign('title', $title);

        // add options
        if ($assignOptions = $this->parseOption('assignOptions')) {
            foreach ($assignOptions as $optionName) {
                $mail->assign($optionName, $this->parseOption($optionName));
            }
        }

        if (!empty($replyToRecipients)) {
            $mail->replyTo(...$replyToRecipients);
        }

        if (!empty($carbonCopyRecipients)) {
            $mail->cc(...$carbonCopyRecipients);
        }

        if (!empty($blindCarbonCopyRecipients)) {
            $mail->bcc(...$blindCarbonCopyRecipients);
        }

        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        if ($attachUploads) {
            foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
                if (!$element instanceof FileUpload) {
                    continue;
                }
                $file = $formRuntime[$element->getIdentifier()];
                if ($file) {
                    if ($file instanceof FileReference) {
                        $file = $file->getOriginalResource();
                    }
                    $mail->attach($file->getContents(), $file->getName(), $file->getMimeType());
                }
            }
        }

        /** todo: remove when support for TYPO3 v11 is dropped */
        if ($version->getMajorVersion() < 12) {
            $mail->send();
        } else{
            GeneralUtility::makeInstance(MailerInterface::class)->send($mail);
        }
    }

}
