<?php

/***************************************************************
 * Extension Manager/Repository config file
 *
 * Auto generated by Extension Builder
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'Forminator',
	'description' => 'This extension comes with several additional validators, finishers, formElements and general improvements for the usage of the typo3/cms-form-extension, e.g. improved ConfirmationMessage- and Email-Finisher, checkboxes with link-able labels for GDPR or terms & conditions, improved email-validation, validator for phone-numbers, ViewHelper for well formated plaintext-emails.',
	'category' => 'plugin',
	'author' => 'Steffen Kroggel',
	'author_email' => 'developer@steffenkroggel.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'clearCacheOnLoad' => 0,
	'versiong' => '12.4.11',
	'constraints' => [
		'depends' => [
            'typo3' => '10.4.99-12.4.99',
        ],
		'conflicts' => [
		],
		'suggests' => [
            'form_consent' => '2.2.0-2.2.99',
            'hcaptcha' => '2.2.0-2.2.99',
        ],
	],
];
