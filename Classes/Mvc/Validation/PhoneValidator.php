<?php
declare(strict_types=1);
namespace Madj2k\Forminator\Mvc\Validation;

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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class PhoneValidator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class PhoneValidator extends AbstractValidator
{

	/**
	 * This contains the supported options, their default values, types and descriptions.
	 *
	 * @var array
	 */
	protected $supportedOptions = [
		'allowLeadingPlus' => [false, 'Allows leading plus-sign in telephone-number for country-code.', 'boolean']
	];


	/**
	 * Check for valid phone-number
	 *
	 * Valid are numbers, slashes and a leading plus-sign (country-code)
	 */
	public function isValid($value): void
	{
		// leading plus allowed?
		if ($this->options['allowLeadingPlus']) {
			$value = ltrim($value, '+');
		}

		// Check phone-number
		if (preg_match('/[^0-9\/]+/', $value)) {

			$this->addError(
				$this->translateErrorMessage(
					'validation.error.1724504891',
					'form'
				),
				1724504891
			);
		}
	}
}
