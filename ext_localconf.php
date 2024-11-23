<?php

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
    },
	'forminator'
);


