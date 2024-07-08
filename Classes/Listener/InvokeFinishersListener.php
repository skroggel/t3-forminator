<?php
declare(strict_types=1);
namespace Madj2k\Forminator\Listener;

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

use EliasHaeussler\Typo3FormConsent\Compatibility;
use EliasHaeussler\Typo3FormConsent\Domain;
use EliasHaeussler\Typo3FormConsent\Event;
use EliasHaeussler\Typo3FormConsent\Type;
use Psr\Http\Message;
use TYPO3\CMS\Core;
use TYPO3\CMS\Extbase;
use TYPO3\CMS\Form;
use TYPO3\CMS\Frontend;


/**
 * Class InvokeFinishersListener
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @author Elias Häußler <elias@haeussler.dev>
 * @package Madj2k_Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @see \EliasHaeussler\Typo3FormConsent\Event\Listener\InvokeFinishersListener
 */
final class InvokeFinishersListener
{
    private readonly Core\Information\Typo3Version $typo3Version;

    public function __construct(
        private readonly Form\Mvc\Persistence\FormPersistenceManagerInterface $formPersistenceManager,
        private readonly Core\Domain\Repository\PageRepository $pageRepository,
    ) {
        $this->typo3Version = new Core\Information\Typo3Version();
    }

    public function onConsentApprove(Event\ApproveConsentEvent $event): void
    {
        $response = $this->invokeFinishers($event->getConsent(), 'isConsentApproved()');
        $event->setResponse($response);
    }

    public function onConsentDismiss(Event\DismissConsentEvent $event): void
    {
        $response = $this->invokeFinishers($event->getConsent(), 'isConsentDismissed()');
        $event->setResponse($response);
    }

    private function invokeFinishers(Domain\Model\Consent $consent, string $condition): ?Message\ResponseInterface
    {
        // Early return if original request is missing
        // or no finisher variants are configured
        if (
            $consent->getOriginalRequestParameters() === null
            || $consent->getOriginalContentElementUid() === 0
            || !$this->areFinisherVariantsConfigured($consent->getFormPersistenceIdentifier(), $condition)
        ) {
            return null;
        }

        // Migrate legacy HMAC hashes after upgrade to TYPO3 v13
        $consent->setOriginalRequestParameters(
            $this->migrateOriginalRequestParameters($consent->getOriginalRequestParameters()),
        );

        // Re-render form to invoke finishers
        $request = $this->createRequestFromOriginalRequestParameters($consent->getOriginalRequestParameters());

        return $this->dispatchFormReRendering($consent, $request);
    }

    private function dispatchFormReRendering(
        Domain\Model\Consent $consent,
        Message\ServerRequestInterface $serverRequest,
    ): ?Message\ResponseInterface {
        // Fetch record of original content element
        $contentElementRecord = $this->fetchOriginalContentElementRecord($consent->getOriginalContentElementUid());

        // Early return if content element record cannot be resolved
        if (!\is_array($contentElementRecord)) {
            return null;
        }

        // Build extbase bootstrap object
        $contentObjectRenderer = Core\Utility\GeneralUtility::makeInstance(Frontend\ContentObject\ContentObjectRenderer::class);
        $contentObjectRenderer->start($contentElementRecord, 'tt_content', $serverRequest);
        $contentObjectRenderer->setUserObjectType(Frontend\ContentObject\ContentObjectRenderer::OBJECTTYPE_USER_INT);
        $bootstrap = Core\Utility\GeneralUtility::makeInstance(Extbase\Core\Bootstrap::class);
        $bootstrap->setContentObjectRenderer($contentObjectRenderer);

        // Inject content object renderer (TYPO3 >= 12)
        $serverRequest = $serverRequest->withAttribute('currentContentObject', $contentObjectRenderer);

        $listTypeExploded = explode('_', $contentElementRecord['CType']);

        /** SK: small change, big impact */
        $extensionName = 'Form';
        $pluginName = 'Formframework';
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'] as $tempExtensionName => $tempExtensionConfig) {
            if (strtolower($tempExtensionName) === $listTypeExploded[0]) {
                $extensionName = $tempExtensionName;
                foreach ($tempExtensionConfig['plugins'] as $tempPluginName => $tempPluginConfig) {
                    if (strtolower($tempPluginName) === $listTypeExploded[1]) {
                        $pluginName = $tempPluginName;
                    }
                }
            }
        }

        $configuration = [
            'extensionName' => $extensionName,
            'pluginName' => $pluginName
        ];

        /** SK: END */

        try {
            // Dispatch extbase request
            $content = $bootstrap->run('', $configuration, $serverRequest);
            $response = new Core\Http\Response();
            $response->getBody()->write($content);

            return $response;
        } catch (Core\Http\ImmediateResponseException|Core\Http\PropagateResponseException $exception) {
            // If any immediate response is thrown, use this for further processing
            return $exception->getResponse();
        }
    }

    /**
     * @param Type\JsonType<string, array<string, array<string, mixed>>> $originalRequestParameters
     * @return Type\JsonType<string, array<string, array<string, mixed>>>
     */
    private function migrateOriginalRequestParameters(Type\JsonType $originalRequestParameters): Type\JsonType
    {
        // Migration is only needed when upgrading from TYPO3 < v13
        if ($this->typo3Version->getMajorVersion() < 13) {
            return $originalRequestParameters;
        }

        $migration = new Compatibility\Migration\HmacHashMigration();
        $parameters = $originalRequestParameters->toArray();

        array_walk_recursive($parameters, static function(mixed &$value, string|int $key) use ($migration): void {
            if (!is_string($value) || !is_string($key)) {
                return;
            }

            // Migrate EXT:extbase and EXT:form hash scopes
            $hashScope = Form\Security\HashScope::tryFrom($key) ?? Extbase\Security\HashScope::tryFrom($key);

            if ($hashScope !== null) {
                $value = $migration->migrate($value, $hashScope->prefix());
            }
        });

        return Type\JsonType::fromArray($parameters);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchOriginalContentElementRecord(int $contentElementUid): ?array
    {
        // Early return if content element UID cannot be  determined
        if ($contentElementUid === 0) {
            return null;
        }

        // Fetch content element record
        $record = $this->pageRepository->checkRecord('tt_content', $contentElementUid);

        // Early return if content element record cannot be resolved
        if (!\is_array($record)) {
            return null;
        }

        return $this->pageRepository->getLanguageOverlay('tt_content', $record);
    }

    /**
     * @param Type\JsonType<string, array<string, array<string, mixed>>> $originalRequestParameters
     */
    private function createRequestFromOriginalRequestParameters(Type\JsonType $originalRequestParameters): Message\ServerRequestInterface
    {
        return $this->getServerRequest()
            ->withMethod('POST')
            ->withParsedBody($originalRequestParameters->toArray());
    }

    private function areFinisherVariantsConfigured(string $formPersistenceIdentifier, string $condition): bool
    {
        $formConfiguration = $this->formPersistenceManager->load($formPersistenceIdentifier);

        foreach ($formConfiguration['variants'] ?? [] as $variant) {
            if (str_contains($variant['condition'] ?? '', $condition) && isset($variant['finishers'])) {
                return true;
            }
        }

        return false;
    }

    private function getServerRequest(): Message\ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? Core\Http\ServerRequestFactory::fromGlobals();
    }
}
