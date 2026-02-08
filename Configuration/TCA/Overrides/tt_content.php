<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extensionKey)
	{

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form_consent')) {

            $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
            $version = $typo3Version->getMajorVersion();
            
            if ($version < 13) {
                $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formconsent_consent'] = 'pi_flexform';
            } else {
                $GLOBALS['TCA']['tt_content']['types']['formconsent_consent']['showitem'] .= 'pi_flexform';
            }
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                'formconsent_consent',
                'FILE:EXT:' . $extensionKey . '/Configuration/FlexForms/FormConsent.xml'
            );
        }

	},
	'forminator'
);
