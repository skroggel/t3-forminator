<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\Record;

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

/**
 * Class GetByFormParamViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class GetByFormParamViewHelper extends AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
        $this->registerArgument('formIdentifier', 'string', 'The form identifier.', true);
        $this->registerArgument('param', 'string', 'The parameter name to get from the request.', true);
    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return mixed
     * @throws \Doctrine\DBAL\Exception
     */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	): array {

        /** @var string $param */
        $param = $arguments['param'];

        /** @var string $param */
        $formIdentifier = $arguments['formIdentifier'];

        /** @var string $table */
        $table = $arguments['table'];

        /** @var string $field */
        $field = $arguments['field'];

        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $renderingContext->getRequest();

        if ($params = $request->getParsedBody()) {
            if (!empty($params['tx_form_formframework'][$formIdentifier][$param])) {
                if ($uid = (int) $params['tx_form_formframework'][$formIdentifier][$param]) {
                    return self::findByUid($uid, $table, $field);
                }
            }
        }

        return [];
	}
}
