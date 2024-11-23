<?php
declare(strict_types=1);
namespace Madj2k\Forminator\Domain\Finishers;

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

use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

/**
 * Class ConfirmationMessageFinisher
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ConfirmationMessageFinisher extends AbstractConfirmationMessageFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = [
        'contentElementUid' => 0,
        'typoscriptObjectPath' => 'lib.tx_form.contentElementRendering',
        'assignOptions' => []
    ];


	/**
	 * Executes this finisher
	 *
	 * @see AbstractFinisher::execute()
	 * @return string
	 * @throws \TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException
	 */
	protected function executeInternal(): string
	{
		$standaloneView = $this->initializeStandaloneView(
			$this->finisherContext->getFormRuntime()
		);

        if ($assignOptions = $this->parseOption('assignOptions')) {
            foreach ($assignOptions as $optionName) {
                $standaloneView->assign($optionName,$this->parseOption($optionName));
            }
        }
		return $standaloneView->render();
	}
}
