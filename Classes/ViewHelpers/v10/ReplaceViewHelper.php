<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\v10;

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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ReplaceViewHelper
 *
 * Backport of \TYPO3Fluid\Fluid\ViewHelpers\ReplaceViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @todo remove when support for v10 is dropped
 */
final class ReplaceViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
        $this->registerArgument('search', 'mixed', '');
        $this->registerArgument('replace', 'mixed', '', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $value = $this->arguments['value'] ?? $this->renderChildren();
        $search = $this->arguments['search'];
        $replace = $this->arguments['replace'];
        if ($value === null || (!is_scalar($value) && !$value instanceof \Stringable)) {
            throw new \InvalidArgumentException('A stringable value must be provided.', 1710441987);
        }
        if ($search === null) {
            if (!is_iterable($replace)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument "replace" must be iterable to be used without "search" argument, "%s" given instead.',
                    get_debug_type($replace),
                ), 1710441988);
            }

            $replace = iterator_to_array($replace);

            $search = array_keys($replace);
            $replace = array_values($replace);
        } else {
            if (!is_iterable($search) && !is_scalar($search)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument "search" must be either iterable or scalar, "%s" given instead.',
                    get_debug_type($search),
                ), 1710441989);
            }
            if (!is_iterable($replace) && !is_scalar($replace)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument "replace" must be either iterable or scalar, "%s" given instead.',
                    get_debug_type($replace),
                ), 1710441990);
            }

            $search = is_iterable($search) ? iterator_to_array($search) : [$search];
            $replace = is_iterable($replace) ? iterator_to_array($replace) : [$replace];

            if (\count($search) !== \count($replace)) {
                throw new \InvalidArgumentException('Count of "search" and "replace" arguments must be the same.', 1710441991);
            }
        }
        return str_replace($search, $replace, (string)$value);
    }
}
