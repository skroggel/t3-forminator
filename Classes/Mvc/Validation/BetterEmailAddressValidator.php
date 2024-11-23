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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class BetterEmailAddressValidator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class BetterEmailAddressValidator extends AbstractValidator
{

	/**
	 * This contains the supported options, their default values, types and descriptions.
	 *
	 * @var array
	 */
	protected $supportedOptions = [

	];


    /**
     * Check for valid email
     */
    public function isValid($value): void
    {
		// core method seems to have a bug that does not check for FQDN (since TYPO3 v10), so we double-check
		if (
			(! GeneralUtility::validEmail($value)
			|| (! filter_var($value, FILTER_VALIDATE_EMAIL)))
		) {
			$this->addError(
				$this->translateErrorMessage(
					'validation.error.1221559976',
					'form'
				),
				1221559976
			);
		}
    }
}
