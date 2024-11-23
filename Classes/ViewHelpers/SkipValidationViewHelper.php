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

use TYPO3\CMS\Form\Domain\Model\FormElements\Page;
use TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class SkipValidationViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class SkipValidationViewHelper extends AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
		$this->registerArgument('formElement', RenderableInterface::class, 'The form-element.', true);
	}


	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return bool
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	): bool {

		/** @var \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $formElement */
		$formElement = $arguments['formElement'];

		return self::hasSkipValidationInPage($formElement);
	}


	/**
	 * Check if the skipValidation-renderingOption is set in the page
	 *
	 * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $formElement
	 * @return bool
	 */
	protected static function hasSkipValidationInPage(RenderableInterface $formElement): bool
	{

		/** @var \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $parentFormElement */
		if ($parentFormElement = $formElement->getParentRenderable()) {

			if ($parentFormElement instanceof Page) {
				if ($renderingOptions = $parentFormElement->getRenderingOptions()) {
					if ($renderingOptions['skipValidation']) {
						return true;
					}
				}
				return false;
			}

			if ($parentFormElement instanceof RenderableInterface) {
				return self::hasSkipValidationInPage($parentFormElement);
			}
		}

		return false;
	}

}
