<?php
declare(strict_types=1);
namespace Madj2k\Forminator\EventListener;

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

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class ModifyFlexFormEvent
 *
 * @author Georg Ringer <mail@ringer.it>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @see https://docs.typo3.org/p/georgringer/news/main/en-us/Tutorials/ExtendNews/ExtendFlexforms/Index.html
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ModifyFlexFormEvent
{

	/**
	 * @const string
	 */
	protected const EXTENSION_NAME = 'forminator';


	/**
	 * @const array
	 */
	protected const FLEXFORM_EXTEND_FILES = [
		'plugin_name' => [
			0 => [
				'file' => 'File.xml',
			],
		]
	];


	/**
     * Called as event
     *
	 * @param \TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent $event
	 * @return void
	 */
	public function __invoke(\TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent $event): void
	{
		$dataStructure = $event->getDataStructure();
		$identifier = $event->getIdentifier();
		$event->setDataStructure($this->addFlexForms($identifier, $dataStructure));
	}


    /**
     * Called as hook
     *
     * @param array $dataStructure
     * @param array $identifier
     * @return array
     * @todo remove when support for TYPO3 v10 is dropped
     */
    public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
    {
        return $this->addFlexForms($identifier, $dataStructure);
    }


    /**
     * @param array $identifier
     * @param array $dataStructure
     * @return array
     */
    protected function addFlexForms(array $identifier, array $dataStructure): array
    {

        if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content') {

            // got through extend-settings
            foreach (self::FLEXFORM_EXTEND_FILES as $cType => $flexforms) {

                // check for CType
                if ($identifier['dataStructureKey'] === '*,' . $cType) {

                    foreach($flexforms as $config) {

                        // check if we have a condition to consider - or simply extend
                        $extendFlexForm = true;
                        if (
                            (isset($config['condition']))
                            && (isset($config['condition']['field']))
                            && (isset($config['condition']['value']))
                        ){
                            $extendFlexForm = false;
                            if (
                                ($value = $identifier[$config['condition']['field']])
                                && ($value == $config['condition']['value'])
                            ){
                                $extendFlexForm = true;
                            }
                        }

                        if ($extendFlexForm) {
                            $file = GeneralUtility::getFileAbsFileName(
                                'EXT:' . self::EXTENSION_NAME . '/Configuration/FlexForms/' . $config['file']
                            );
                            $content = file_get_contents($file);
                            if ($content) {
                                ArrayUtility::mergeRecursiveWithOverrule(
                                    $dataStructure['sheets'],
                                    GeneralUtility::xml2array($content)
                                );
                            }
                        }
                    }
                }
            }
        }

        return $dataStructure;
    }
}
