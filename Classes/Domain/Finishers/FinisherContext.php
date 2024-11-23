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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Form\Domain\Finishers\FinisherVariableProvider;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;

/**
 * Class FinisherContext
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FinisherContext extends \TYPO3\CMS\Form\Domain\Finishers\FinisherContext
{

	private Request $request;

	/**
	 * @internal
	 */
	public function __construct(FormRuntime $formRuntime, Request $request)
	{
		$this->formRuntime = $formRuntime;
		$this->request = $request;
		$this->finisherVariableProvider = GeneralUtility::makeInstance(FinisherVariableProvider::class);
	}


	/**
	 * @return \TYPO3\CMS\Extbase\Mvc\Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}


	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Request $request
	 * @return \TYPO3\CMS\Extbase\Mvc\Request
	 */
    public function setRequest(Request $request): Request
    {
        return $this->request = $request;
    }
}
