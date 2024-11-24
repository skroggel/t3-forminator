<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\Email;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class PlaintextLineBreaksViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @see https://github.com/skroggel/typo3-postmaster/blob/master/Classes/ViewHelpers/Email/PlaintextLineBreaksViewHelper.php
 */
 final class PlaintextLineBreaksViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     *
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'string', 'String to format');
        $this->registerArgument('convertLineBreaks', 'boolean', 'Convert line-breaks to \n.', false, false);
    }


    /**
     * Handles line breaks and indents in plaintext mode
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
	 * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {

        $value = $renderChildrenClosure();
        $convertLineBreaks = (bool) ($arguments['convertLineBreaks'] ?: false);

        // convert line breaks to manual line breaks
        if ($convertLineBreaks) {
            $value = preg_replace( '/\r|\n/', '\n',  $value);
        }

        // replace real line breaks and indents
        $value = preg_replace("/\r|\n|\t|([ ]{2,})/", '', trim($value));

        // convert manual line breaks - only if no convertLineBreaks-attribute given!
        if (! $convertLineBreaks) {
            $value = str_replace('\n', "\n", $value);
        }

        // replace manual spaces
        $value = str_replace('\s', " ", $value);


        return $value;
    }
}
