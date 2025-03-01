<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers;

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
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetElementByIdentifierViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class GetElementValueByIdentifierViewHelper extends AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
		$this->registerArgument('identifier', 'string', 'The element id to fetch the value from', false);
	}


	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return mixed
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	) {

		/** @var string $identifier */
		if (
            isset($arguments['identifier'])
            && ($identifier = $arguments['identifier'])
        ){

			/** @var \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime */
			$formRuntime = $renderingContext
				->getViewHelperVariableContainer()
				->get(RenderRenderableViewHelper::class, 'formRuntime');

			if (isset($formRuntime[$identifier])) {
				return $formRuntime[$identifier];
			}
		}

		return null;
	}
}
