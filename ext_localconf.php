<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{
        $version = GeneralUtility::makeInstance(Typo3Version::class);
        //=================================================================
        // XClasses
        //=================================================================
        if ($version->getMajorVersion() >= 12) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\Domain\Finishers\FinisherContext::class] = [
                'className' => \Madj2k\Forminator\Domain\Finishers\FinisherContext::class
            ];
        }

        //=================================================================
        // Hooks
        //=================================================================
        if ($version->getMajorVersion() < 12) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
                = \Madj2k\Forminator\EventListener\ModifyFlexFormEvent::class;

        }

        //=================================================================
        // Add form configuration
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            $extKey,
            'setup',
            'module.tx_form {
                settings {
                    yamlConfigurations {
                        1727248952 = EXT:forminator/Configuration/Yaml/FormSetup.yaml
                    }
                }
            }
            plugin.tx_form {
                settings {
                    yamlConfigurations {
                        1727248952 = EXT:forminator/Configuration/Yaml/FormSetup.yaml
                    }
                }
            }'
        );
    },
	'forminator'
);


