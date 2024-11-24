<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\Configuration;

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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AddToEmailFinisherAsReceiverViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class AddToEmailFinisherAsReceiverViewHelper extends AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
		$this->registerArgument('formConfiguration', 'array', 'The form configuration.', true);
        $this->registerArgument('finisherIdentifier', 'string', 'The finisher identifier.', false, 'emailToReceiver');
        $this->registerArgument('email', 'string', 'The email to add.', true);
        $this->registerArgument('name', 'string', 'The name to add.', false, '');
        $this->registerArgument('override', 'bool', 'If set to true, the existing recievers are overriden.', false, false);


    }


	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return array
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	): array {

		/** @var array $config */
		$formConfiguration = $arguments['formConfiguration'];

        /** @var string $finisher */
        $finisher = lcfirst($arguments['finisherIdentifier'] ?? '');

        /** @var string $email */
        $email = lcfirst($arguments['email'] ?? '');

        /** @var string $name */
        $name = $arguments['name'] ?? '';

        /** @var bool $override */
        $override = $arguments['override'] ?? '';

        if (
            (!empty ($finisher))
            && (!empty ($email))
            && (!empty ($formConfiguration['finishers']))
            && (!empty ($formConfiguration['finishers'][$finisher]))
        ){
            if (! isset($formConfiguration['finishers'][$finisher]['options'])) {
                $formConfiguration['finishers'][$finisher]['options'] = [];
            }

            if (
                (! isset($formConfiguration['finishers'][$finisher]['options']['recipients']))
                || ($override)
            ){
                $formConfiguration['finishers'][$finisher]['options']['recipients'] = [];
            }

            $formConfiguration['finishers'][$finisher]['options']['recipients'][$email] = ($name ?: $email);
        }

        return $formConfiguration;
	}
}
