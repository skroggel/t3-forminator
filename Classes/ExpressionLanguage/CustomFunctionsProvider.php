<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ExpressionLanguage;

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

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * Class CustomFunctionsProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CustomFunctionsProvider  extends AbstractProvider
{

	public function __construct()
	{
		$this->expressionLanguageProviders = [
			IsNullFunctionsProvider::class,
		];
	}

}
