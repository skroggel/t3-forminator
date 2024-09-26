<?php
declare(strict_types=1);
namespace Madj2k\Forminator\Domain\Finishers;

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

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\ConfirmationFinisher;
use TYPO3\CMS\Form\Domain\Finishers\ConfirmationMessageFinisher;

/**
 * Class AbstractConfirmationMessageFinisher
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

/** todo: remove when support for TYPO3 v11 is dropped */
$version = GeneralUtility::makeInstance(Typo3Version::class);
if ($version->getMajorVersion() < 12) {
    abstract class AbstractConfirmationMessageFinisher extends ConfirmationFinisher
    {

    }
} else {
    abstract class AbstractConfirmationMessageFinisher extends ConfirmationMessageFinisher
    {

    }
}

