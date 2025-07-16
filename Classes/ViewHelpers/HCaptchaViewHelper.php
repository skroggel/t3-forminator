<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers;

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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Waldhacker\Hcaptcha\Service\ConfigurationService;

/**
 * Class HCaptchaViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
if (class_exists(\Waldhacker\Hcaptcha\Service\ConfigurationService::class)) {
    final class HCaptchaViewHelper extends AbstractViewHelper
    {

        protected $escapeOutput = false;


        /**
         * @var \Waldhacker\Hcaptcha\Service\ConfigurationService|null
         */
        private ?ConfigurationService $configurationService = null;


        /**
         * @param \Waldhacker\Hcaptcha\Service\ConfigurationService $configurationService
         */
        public function injectConfigurationService(ConfigurationService $configurationService): void
        {
            $this->configurationService = $configurationService;
        }


        /**
         * Renders the captcha also in AJAX-context
         *
         * @return string
         */
        public function render(): string
        {
            /** @var \TYPO3\CMS\Form\Domain\Runtime\FormRuntime|null $formRuntime */
            $formRuntime = $this->renderingContext
                ->getViewHelperVariableContainer()
                ->get(RenderRenderableViewHelper::class, 'formRuntime');

            if ($formRuntime) {
                $renderingOptions = $formRuntime->getRenderingOptions();
                if (isset($renderingOptions['previewMode']) && $renderingOptions['previewMode'] === true) {
                    return '';
                }
            }

            $languageCode = '';
            try {
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $request = $this->renderingContext->getRequest();

                /** @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage $language */
                $language = $request->getAttribute('language');

                // depending on version
                $versionUtility = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
                $currentVersion = (int)$versionUtility->getMajorVersion();
                if ($currentVersion <= 12) {
                    $languageCode = $language->getTwoLetterIsoCode();
                } else {
                    $languageCode = $language->getLocale()->getLanguageCode(); // v13+
                }

            } catch (\Exception $e) {
                // do nothing
            }

            $hash = md5(microtime());
            $containerId = 'hcaptcha-container' . $hash;
            $functionId = 'hcaptchaFunction' . $hash;

            // ECMA 6 is not working with hCaptcha callback!
            return '<div id="' . $containerId . '" class="captcha"></div>'.
                '<script
                  src="https://js.hcaptcha.com/1/api.js?onload=' . $functionId  . '&render=explicit&hl=' . $languageCode .'"
                  async
                  defer
                ></script>' .
                '<script type="text/javascript">
                    var ' . $functionId   . ' = function () {
                        hcaptcha.render("' . $containerId . '", {
                            sitekey: "' . $this->configurationService->getPublicKey()  . '",
                        });
                    };
                </script>';
        }
    }
} else {
    final class HCaptchaViewHelper extends AbstractViewHelper
    {
        /**
         * @return string
         */
        public function render(): string
        {
            return 'Only works when "dreistromland/typo3-hcaptcha" is installed';
        }
    }
}
