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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Class IsNullFunctionsProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IsNullFunctionsProvider implements ExpressionFunctionProviderInterface
{

	/**
	 * @return ExpressionFunction[]
	 */
	public function getFunctions():array
	{
		return [
			$this->isNullFunction(),
		];
	}


	/**
	 * @return \Symfony\Component\ExpressionLanguage\ExpressionFunction
	 */
	protected function isNullFunction(): ExpressionFunction
	{
		return new ExpressionFunction(
			'isNull',
			static fn () => null, // Not implemented, we only use the evaluator
			static function ($arguments, $value) {
				return is_null($value);
			}
		);
	}

}
