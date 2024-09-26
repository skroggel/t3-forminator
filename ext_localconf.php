<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{
        $version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);

        //=================================================================
        // Hooks
        //=================================================================
        if ($version->getMajorVersion() < 12) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
                = \Madj2k\Forminator\EventListener\ModifyFlexFormEvent::class;
        }

        //=================================================================
        // XClasses
        //=================================================================
        if ($version->getMajorVersion() >= 12) {
            if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form_consent')) {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\EliasHaeussler\Typo3FormConsent\Event\Listener\InvokeFinishersListener::class] = [
                    'className' => \Madj2k\Forminator\Listener\InvokeFinishersListener::class
                ];
            }
        }

    },
	'forminator'
);


