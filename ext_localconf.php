<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{
        //=================================================================
        // XClasses
        //=================================================================
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form_consent')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\EliasHaeussler\Typo3FormConsent\Event\Listener\InvokeFinishersListener::class] = [
                'className' => \Madj2k\Forminator\Listener\InvokeFinishersListener::class
            ];
        }
    },
	'forminator'
);


