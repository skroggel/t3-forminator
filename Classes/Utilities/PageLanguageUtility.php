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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class PageLanguageUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageLanguageUtility
{

	/**
	 * Gets the language uid from the request-object
	 *
	 * @return int
	 */
	public static function getCurrentLanguageId(): int
	{
		/** @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage $siteLanguage */
		if ($request = self::getRequest()) {

			/** @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage $siteLanguage */
			$siteLanguage = $request->getAttribute('language');
			return  $siteLanguage->getLanguageId();
		}

		return 0;
	}


	/**
	 * Gets the request-object
	 *
	 * @return \Psr\Http\Message\ServerRequestInterface
	 */
	private static function getRequest(): ServerRequestInterface
	{
		return $GLOBALS['TYPO3_REQUEST'];
	}
}
