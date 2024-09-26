<?php
declare(strict_types=1);
namespace Madj2k\Forminator\Utilities;

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

use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PasswordUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PasswordUtility
{
	/**
	 * Encrypt the password
	 *
	 * @param string $plaintextPassword
	 * @param string $mode
	 * @return string
	 * @throws \Exception
	 * @see https://github.com/skroggel/typo3-fe-register/blob/master/Classes/Utility/PasswordUtility.php#L102
	 */
	public static function saltPassword(string $plaintextPassword, string $mode  = 'FE'): string
	{
		try {
			/** @var \TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory $passwordHashFactory */
			$passwordHashFactory = GeneralUtility::makeInstance( PasswordHashFactory::class);
			$objSalt = $passwordHashFactory->getDefaultHashInstance($mode);
			if (! is_object($objSalt)) {
				throw new \Exception('SaltFactory is not an object!');
			}
			return $objSalt->getHashedPassword($plaintextPassword);
		} catch (\Exception $e) {
			throw new \Exception('Something went wrong while trying to salt password!');
		}
	}


	/**
	 * Check if password is encrypted
	 *
	 * @param string $password
	 * @param string $mode
	 * @return bool
	 * @throws \Exception
	 */
	public static function isSaltedPassword(string $password, string $mode  = 'FE'): bool
	{
		try {
			/** @var \TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory $passwordHashFactory */
			$passwordHashFactory = GeneralUtility::makeInstance( PasswordHashFactory::class);
			$objSalt = $passwordHashFactory->getDefaultHashInstance($mode);
			if (! is_object($objSalt)) {
				throw new \Exception('SaltFactory is not an object!');
			}
			return $objSalt->isValidSaltedPW($password);
		} catch (\Exception $e) {
			throw new \Exception('Something went wrong while trying to check the password!');
		}
	}
}
