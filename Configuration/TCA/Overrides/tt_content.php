<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extensionKey)
	{

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form_consent')) {
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formconsent_consent'] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                'formconsent_consent',
                'FILE:EXT:' . $extensionKey . '/Configuration/FlexForms/FormConsent.xml'
            );
        }

	},
	'forminator'
);
