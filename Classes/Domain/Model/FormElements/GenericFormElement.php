<?php
namespace Madj2k\Forminator\Domain\Model\FormElements;

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

use Madj2k\Forminator\Mvc\Validation\NotEmptyNumericValidator;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;

/**
 * Class GenericFormElement
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GenericFormElement extends \TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement
{

    /**
     * Check if the element is required
     */
    public function isRequired(): bool
    {
        foreach ($this->getValidators() as $validator) {
            if (
                ($validator instanceof NotEmptyValidator)
                || ($validator instanceof NotEmptyNumericValidator)
            ){
                return true;
            }
        }
        return false;
    }
}
